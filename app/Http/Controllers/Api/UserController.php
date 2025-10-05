<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        return response()->json(User::paginate($perPage));
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'mobile' => 'nullable|string|unique:users,mobile',
            'password' => 'nullable|string|min:6',
            'role' => ['nullable', Rule::in(['customer','staff','admin'])],
            'app_version' => 'nullable|string',
            'device_id' => 'nullable|string',
            'meta' => 'nullable|array',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user = User::create($data);

        return response()->json(['status' => 'created', 'user' => $user], 201);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes','nullable','email', Rule::unique('users','email')->ignore($user->id)],
            'mobile' => ['sometimes','nullable','string', Rule::unique('users','mobile')->ignore($user->id)],
            'password' => 'sometimes|nullable|string|min:6',
            'role' => ['sometimes', Rule::in(['customer','staff','admin'])],
            'is_active' => 'sometimes|boolean',
            'app_version' => 'nullable|string',
            'device_id' => 'nullable|string',
            'meta' => 'nullable|array',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        return response()->json(['status' => 'updated', 'user' => $user]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['status' => 'deleted']);
    }

    public function checkMobile(Request $request)
    {
        $v = $request->validate(['mobile' => 'required|string']);
        $exists = User::where('mobile', $v['mobile'])->exists();
        return response()->json(['exists' => $exists]);
    }
}
