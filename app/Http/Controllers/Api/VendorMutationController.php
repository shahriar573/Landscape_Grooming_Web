<?php

namespace App\Http\Controllers\Api;

use App\Events\VendorUpdated;
use App\Http\Controllers\Controller;
use App\Models\CollabEvent;
use App\Models\CollabSession;
use App\Models\Vendor;
use App\Models\VendorRevision;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VendorMutationController extends Controller
{
    /**
     * Apply a mutation to vendor data with optimistic concurrency control
     */
    public function apply(Request $request, Vendor $vendor): JsonResponse
    {
        $this->authorize('collaborate', $vendor);

        $validated = $request->validate([
            'session_id' => 'required|exists:collab_sessions,id',
            'field' => 'required|string|in:name,email,phone,website,address,description,metadata',
            'value' => 'required',
            'expected_version' => 'required|integer|min:0',
            'operation' => 'nullable|string|in:set,merge,delete',
        ]);

        // Verify session is active
        $session = CollabSession::findOrFail($validated['session_id']);
        
        if ($session->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Session does not belong to this vendor'], 403);
        }

        if ($session->ended_at) {
            return response()->json(['message' => 'Session has ended'], 410);
        }

        try {
            $result = DB::transaction(function () use ($vendor, $validated, $request, $session) {
                // Get current vendor state with lock
                $vendor->lockForUpdate()->find($vendor->id);
                
                // Get latest revision to check version
                $latestRevision = VendorRevision::where('vendor_id', $vendor->id)
                    ->orderBy('id', 'desc')
                    ->first();

                $currentVersion = $latestRevision?->id ?? 0;

                // Check for version conflict (optimistic locking)
                if ($currentVersion !== $validated['expected_version']) {
                    throw ValidationException::withMessages([
                        'version' => [
                            'Conflict detected. Expected version ' . $validated['expected_version'] . 
                            ' but current version is ' . $currentVersion . 
                            '. Please refresh and try again.'
                        ],
                    ]);
                }

                // Store before state
                $beforeState = $vendor->only([
                    'name', 'email', 'phone', 'website', 'address', 'description', 'metadata'
                ]);

                // Apply the mutation
                $field = $validated['field'];
                $value = $validated['value'];
                $operation = $validated['operation'] ?? 'set';

                if ($operation === 'set') {
                    $vendor->{$field} = $value;
                } elseif ($operation === 'merge' && $field === 'metadata') {
                    $currentMeta = $vendor->metadata ?? [];
                    $vendor->metadata = array_merge($currentMeta, $value);
                } elseif ($operation === 'delete') {
                    $vendor->{$field} = null;
                }

                $vendor->save();

                // Store after state
                $afterState = $vendor->only([
                    'name', 'email', 'phone', 'website', 'address', 'description', 'metadata'
                ]);

                // Create revision record
                $revision = VendorRevision::create([
                    'vendor_id' => $vendor->id,
                    'collab_event_id' => null, // Will be updated below
                    'created_by' => $request->user()->id,
                    'revision_key' => $field,
                    'before_state' => $beforeState,
                    'after_state' => $afterState,
                ]);

                // Log the mutation as a collab event
                $event = CollabEvent::create([
                    'collab_session_id' => $session->id,
                    'vendor_id' => $vendor->id,
                    'actor_id' => $request->user()->id,
                    'event_type' => 'mutation',
                    'payload' => [
                        'field' => $field,
                        'operation' => $operation,
                        'value' => $value,
                        'user_name' => $request->user()->name,
                    ],
                    'occurred_at' => now(),
                ]);

                // Update revision with event reference
                $revision->update(['collab_event_id' => $event->id]);

                return [
                    'vendor' => $vendor->fresh(),
                    'revision' => $revision,
                    'new_version' => $revision->id,
                ];
            });

            // Broadcast the update to other collaborators
            broadcast(new VendorUpdated(
                $vendor,
                $session,
                $request->user(),
                $validated['field'],
                $validated['value']
            ))->toOthers();

            return response()->json([
                'message' => 'Mutation applied successfully',
                'vendor' => $result['vendor'],
                'version' => $result['new_version'],
                'revision_id' => $result['revision']->id,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Version conflict detected',
                'errors' => $e->errors(),
                'current_state' => $vendor->fresh(),
            ], 409);
        }
    }

    /**
     * Get current vendor state with version
     */
    public function getState(Request $request, Vendor $vendor): JsonResponse
    {
        $this->authorize('view', $vendor);

        $latestRevision = VendorRevision::where('vendor_id', $vendor->id)
            ->orderBy('id', 'desc')
            ->first();

        return response()->json([
            'vendor' => $vendor,
            'version' => $latestRevision?->id ?? 0,
            'last_updated_at' => $vendor->updated_at->toIso8601String(),
            'last_updated_by' => $latestRevision?->author?->name,
        ]);
    }

    /**
     * Get revision history
     */
    public function revisions(Request $request, Vendor $vendor): JsonResponse
    {
        $this->authorize('view', $vendor);

        $perPage = min($request->integer('per_page', 20), 100);

        $revisions = VendorRevision::where('vendor_id', $vendor->id)
            ->with(['author:id,name,email', 'event'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'vendor_id' => $vendor->id,
            'revisions' => $revisions->map(function ($revision) {
                return [
                    'id' => $revision->id,
                    'revision_key' => $revision->revision_key,
                    'author' => $revision->author ? [
                        'id' => $revision->author->id,
                        'name' => $revision->author->name,
                        'email' => $revision->author->email,
                    ] : null,
                    'before_state' => $revision->before_state,
                    'after_state' => $revision->after_state,
                    'created_at' => $revision->created_at->toIso8601String(),
                    'time_ago' => $revision->created_at->diffForHumans(),
                ];
            }),
            'pagination' => [
                'current_page' => $revisions->currentPage(),
                'per_page' => $revisions->perPage(),
                'total' => $revisions->total(),
                'last_page' => $revisions->lastPage(),
            ],
        ]);
    }

    /**
     * Rollback to a specific revision
     */
    public function rollback(Request $request, Vendor $vendor, VendorRevision $revision): JsonResponse
    {
        $this->authorize('manageCollaborators', $vendor);

        if ($revision->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Revision does not belong to this vendor'], 403);
        }

        DB::transaction(function () use ($vendor, $revision, $request) {
            // Apply the before state (rollback)
            $vendor->update($revision->before_state);

            // Create a new revision for the rollback
            VendorRevision::create([
                'vendor_id' => $vendor->id,
                'created_by' => $request->user()->id,
                'revision_key' => 'rollback',
                'before_state' => $revision->after_state,
                'after_state' => $revision->before_state,
            ]);
        });

        return response()->json([
            'message' => 'Vendor rolled back successfully',
            'vendor' => $vendor->fresh(),
        ]);
    }
}
