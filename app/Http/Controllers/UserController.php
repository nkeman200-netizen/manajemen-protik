<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::with(['roles', 'division'])->paginate(15);

        return response()->json([
            'message' => 'Success',
            'data' => $users,
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'division_id' => ['nullable', 'exists:divisions,id'],
            'status' => ['nullable', 'in:active,suspended'],
            'role' => ['nullable', 'string', 'exists:roles,name'],
        ]);

        $user->update(collect($validated)->only(['division_id', 'status'])->filter()->toArray());

        if ($request->has('role')) {
            $user->syncRoles([$validated['role']]);
        }

        $user->load(['roles', 'division']);

        return response()->json([
            'message' => 'Success',
            'data' => $user,
        ]);
    }
}
