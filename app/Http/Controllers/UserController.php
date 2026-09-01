<?php
namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Setting;
use App\Services\SyncService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(private readonly SyncService $syncService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = User::with(['roles', 'division'])
            ->when($request->search, fn ($q, $search) =>
                $q->where(fn ($subQ) =>
                    $subQ->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('nim', 'like', "%{$search}%")
                )
            )
            ->when($request->division_filter, fn ($q, $divName) =>
                $q->whereHas('division', fn ($d) => $d->where('name', $divName))
            )
            ->when($request->division_id, fn ($q, $divId) =>
                $q->where('division_id', $divId)
            )
            ->when($request->prodi_filter ?? $request->prodi, fn ($q, $prodi) => $q->where('prodi', $prodi))
            ->when($request->angkatan_filter ?? $request->angkatan, fn ($q, $angkatan) => $q->where('angkatan', $angkatan));

        $sortBy  = $request->input('sort_by', 'name');
        $sortDir = $request->input('sort_direction', 'asc');

        // Proteksi kolom sorting
        $allowedSorts = ['name', 'nim', 'angkatan', 'prodi'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('name', 'asc');
        }

        // Opsi Pengambilan Seluruh Data (untuk modal/dropdown)
        if ($request->boolean('all') || $request->input('paginate') === 'false') {
            return UserResource::collection($query->get());
        }

        // Mode Paginasi (Default)
        $perPage = $request->integer('per_page', 15);
        $perPage = $perPage > 0 ? $perPage : 15;

        return UserResource::collection($query->paginate($perPage));
    }

    public function filters(Request $request): JsonResponse
    {
        $query = User::query();

        return response()->json([
            'data' => [
                'divisions' => \App\Models\Division::pluck('name'),
                'prodis'    => (clone $query)->whereNotNull('prodi')->where('prodi', '!=', '')->distinct()->pluck('prodi'),
                'angkatans' => (clone $query)->whereNotNull('angkatan')->where('angkatan', '!=', '')->distinct()->pluck('angkatan'),
            ]
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
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

    public function sync(Request $request): JsonResponse
    {
        $url = Setting::where('key', 'bph_master_sync_url')->value('value');
        if (!$url) {
            return response()->json(['message' => 'URL Sinkronisasi Pengurus BPH Pusat belum dikonfigurasi di Pengaturan.'], 500);
        }

        try {
            return response()->json($this->syncService->syncUsers($url));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyinkronisasi data pengurus.', 'error' => $e->getMessage()], 500);
        }
    }
}
