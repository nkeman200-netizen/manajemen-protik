<?php

namespace App\Http\Controllers;

use App\Models\MonthlyDue;
use App\Models\Setting;
use App\Models\User;
use App\Services\SyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonthlyDueController extends Controller
{
    public function __construct(private readonly SyncService $syncService) {}

    public function index(): JsonResponse
    {
        // Mengembalikan struktur untuk Heatmap Frontend
        // PENGECUALIAN: Sembunyikan role 'advisor' dari tabel tagihan kas
        // INJEKSI: Tambahkan 'division' ke Eager Loading untuk kebutuhan Filter/UI Frontend
        $users = User::with(['roles', 'division'])
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'advisor'))
            ->get();
            
        $dues = MonthlyDue::all();
        
        return response()->json([
            'users' => $users,
            'dues'  => $dues,
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $url = Setting::where('key', 'bph_kas_sync_url')->value('value');
        if (!$url) return response()->json(['message' => 'URL Sinkronisasi Kas belum dikonfigurasi di Pengaturan.'], 500);

        try {
            return response()->json($this->syncService->syncMonthlyDues($url));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyinkronisasi data kas.', 'error' => $e->getMessage()], 500);
        }
    }
}
