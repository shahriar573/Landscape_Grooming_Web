<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class VendorController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min($request->integer('per_page', 15), 100);
        $vendors = Vendor::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%' . $request->query('search') . '%';
                $query->where('name', 'like', $term);
            })
            ->when($request->has('is_active'), function ($query) use ($request) {
                $query->where('is_active', $request->boolean('is_active'));
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->appends($request->query());
        return VendorResource::collection($vendors);
    }
    public function store(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $this->validatedData($request);
        $data['slug'] = $data['slug'] ?? $this->slugFromName($data['name']);
        $vendor = Vendor::create($data);
        return (new VendorResource($vendor))->response()->setStatusCode(201);
    }
    public function show(Vendor $vendor): VendorResource
    {
        return new VendorResource($vendor);
    }
    public function update(Request $request, Vendor $vendor): VendorResource
    {
        $this->authorizeAdmin($request);
        $data = $this->validatedData($request, $vendor->id);
        if (!isset($data['slug']) && isset($data['name'])) {
            $data['slug'] = $this->slugFromName($data['name'], $vendor->id);
        }
        $vendor->update($data);
        return new VendorResource($vendor->refresh());
    }
    public function destroy(Request $request, Vendor $vendor): JsonResponse
    {
        $this->authorizeAdmin($request);
        $vendor->delete();
        return response()->json(['message' => 'Vendor deleted']);
    }
    protected function validatedData(Request $request, ?int $ignoreId = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:vendors,slug' . ($ignoreId ? ',' . $ignoreId : ''),
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'metadata' => 'nullable|array',
        ];
        return $request->validate($rules);
    }
    protected function slugFromName(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;
        while (
            Vendor::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }
        return $slug;
    }
    protected function authorizeAdmin(Request $request): void
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Only administrators can manage vendors.');
        }
    }
}
