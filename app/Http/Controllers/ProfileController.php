<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function updateProfile(Request $request): UserResource
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'nim'      => ['nullable', 'string', 'max:50'],
            'phone'    => ['nullable', 'string', 'max:50'],
            'prodi'    => ['nullable', 'string', 'max:100'],
            'angkatan' => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string'],
        ]);

        $request->user()->update($validated);

        return new UserResource($request->user()->fresh()->load(['roles', 'division']));
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Password berhasil diperbarui.',
        ]);
    }
}
