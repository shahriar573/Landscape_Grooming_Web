<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VendorCollaborationController extends Controller
{
    public function __invoke(Request $request, Vendor $vendor): JsonResponse
    {
        $this->authorize('collaborate', $vendor);

        $vendor->loadMissing([
            'owner:id,name,email',
            'collaborators.user:id,name,email',
        ]);

        $activeSession = $vendor->sessions()
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();

        $canManage = Gate::forUser($request->user())->allows('manageCollaborators', $vendor);

        return response()->json([
            'vendor' => [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'owner_id' => $vendor->owner_id,
                'owner' => $vendor->owner ? [
                    'id' => $vendor->owner->id,
                    'name' => $vendor->owner->name,
                    'email' => $vendor->owner->email,
                ] : null,
            ],
            'collaborators' => $vendor->collaborators->map(function ($collaborator) {
                return [
                    'id' => $collaborator->id,
                    'user' => [
                        'id' => $collaborator->user->id,
                        'name' => $collaborator->user->name,
                        'email' => $collaborator->user->email,
                    ],
                    'role' => $collaborator->role,
                    'joined_at' => optional($collaborator->created_at)->toIso8601String(),
                ];
            })->values(),
            'active_session' => $activeSession ? [
                'id' => $activeSession->id,
                'session_uuid' => $activeSession->session_uuid,
                'started_by' => $activeSession->started_by,
                'started_at' => optional($activeSession->started_at)->toIso8601String(),
            ] : null,
            'policy' => [
                'can_manage_collaborators' => $canManage,
            ],
        ]);
    }
}
