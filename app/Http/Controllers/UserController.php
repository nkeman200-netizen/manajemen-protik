<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with(['roles', 'division'])
            ->when($request->search, fn ($q, $search) => 
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
            )
            ->latest()
            ->paginate(15);
            
        return response()->json($users);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'division_id' => 'nullable|exists:divisions,id',
            'status'      => 'required|in:active,suspended',
            'role'        => 'required|in:admin,member,advisor',
        ]);

        $user->update([
            'division_id' => $validated['division_id'],
            'status'      => $validated['status'],
        ]);

        $user->syncRoles([$validated['role']]);

        return response()->json([
            'message' => 'Pengguna berhasil diperbarui',
            'data'    => $user->load(['roles', 'division']),
        ]);
    }
}
