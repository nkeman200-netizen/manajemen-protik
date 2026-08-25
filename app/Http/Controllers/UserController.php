<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'division'])
            ->when($request->search, fn ($q, $search) => 
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
            )
            ->orderBy('name', 'asc'); // Urutkan sesuai alfabet agar rapi
            
        // OPTIMASI: Master Data User kini mengembalikan seluruh data (tanpa paginasi 15 baris)
        // karena jumlah pengurus relatif kecil (30-100) dan butuh akses cepat.
        return response()->json([
            'data' => $query->get()
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'division_id'    => 'nullable|exists:divisions,id',
            'status'         => 'required|in:active,suspended',
            'role'           => 'required|in:admin,member,advisor',
            'is_coordinator' => 'required|boolean',
        ]);

        $user->update([
            'division_id'    => $validated['division_id'],
            'status'         => $validated['status'],
            'is_coordinator' => $validated['is_coordinator'],
        ]);

        $user->syncRoles([$validated['role']]);

        return response()->json([
            'message' => 'Pengguna berhasil diperbarui',
            'data'    => $user->load(['roles', 'division']),
        ]);
    }
}
