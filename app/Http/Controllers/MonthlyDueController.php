<?php

namespace App\Http\Controllers;

use App\Models\MonthlyDue;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthlyDueController extends Controller
{
    public function index(): JsonResponse
    {
        // Mengembalikan struktur untuk Heatmap Frontend
        $users = User::with('roles')->get();
        $dues = MonthlyDue::all();
        
        return response()->json([
            'users' => $users,
            'dues'  => $dues,
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $url = env('TRACKING_KAS_URL');
        if (!$url) return response()->json(['message' => 'URL Sinkronisasi belum dikonfigurasi.'], 500);

        $separator = str_contains($url, '?') ? '&' : '?';
        $freshUrl = $url . $separator . 'cb=' . time();

        try {
            $context = stream_context_create(['http' => ['header' => "Cache-Control: no-cache\r\n"]]);
            $csvData = file_get_contents($freshUrl, false, $context);
            $rows = array_map('str_getcsv', explode("\n", $csvData));
            
            $idIdx = false;
            $dataStartIndex = 0;
            $bulanMap = [
                'JANUARI' => 1, 'FEBRUARI' => 2, 'MARET' => 3, 'APRIL' => 4,
                'MEI' => 5, 'JUNI' => 6, 'JULI' => 7, 'AGUSTUS' => 8,
                'SEPTEMBER' => 9, 'OKTOBER' => 10, 'NOVEMBER' => 11, 'DESEMBER' => 12
            ];
            $bulanIndexes = [];

            // Memindai baris per baris untuk mengakomodasi Multi-Row Headers (Merged Cells)
            foreach ($rows as $index => $row) {
                $cleanRow = array_map(fn($v) => strtoupper(trim($v)), $row);
                
                // Cari Index ID User (Biasanya ada di baris pertama Header)
                if (in_array('ID USER', $cleanRow)) {
                    $idIdx = array_search('ID USER', $cleanRow);
                }

                // Cari Index Bulan (Biasanya ada di baris kedua Header)
                if (in_array('OKTOBER', $cleanRow)) {
                    $dataStartIndex = $index + 1;
                    foreach ($bulanMap as $namaBulan => $angkaBulan) {
                        $idx = array_search($namaBulan, $cleanRow);
                        if ($idx !== false) {
                            $bulanIndexes[$namaBulan] = ['index' => $idx, 'month_num' => $angkaBulan];
                        }
                    }
                    break; // Selesai memindai header
                }
            }

            if (empty($bulanIndexes) || $idIdx === false) {
                return response()->json(['message' => 'Format Header (ID USER pada baris atas, dan OKTOBER pada baris bawah) tidak ditemukan.'], 400);
            }

            $successCount = 0;
            $unmatchedIds = [];

            DB::transaction(function () use ($rows, $dataStartIndex, $idIdx, $bulanIndexes, &$successCount, &$unmatchedIds) {
                for ($i = $dataStartIndex; $i < count($rows); $i++) {
                    $row = $rows[$i];
                    if (empty($row) || count($row) < 3) continue;

                    $userId = trim($row[$idIdx] ?? '');
                    
                    // FIX: Abaikan jika kosong, NaN, atau BUKAN ANGKA (seperti baris "Total Keseluruhan")
                    if (empty($userId) || strtolower($userId) === 'nan' || !is_numeric($userId)) {
                        continue;
                    }

                    $user = User::find($userId);
                    if (!$user) {
                        $unmatchedIds[] = "Baris " . ($i + 1) . " (ID: $userId)";
                        continue; 
                    }

                    $successCount++;

                    foreach ($bulanIndexes as $bData) {
                        $idx = $bData['index'];
                        $monthNum = $bData['month_num'];
                        $valRaw = trim($row[$idx] ?? ''); 
                        
                        if (strtolower($valRaw) === 'nan' || $valRaw === '') {
                            $amount = 0;
                        } else {
                            // Filter format Rupiah: Pisahkan koma desimal, lalu ambil angkanya
                            $valNoDec = explode(',', $valRaw)[0];
                            $amount = (float) preg_replace('/[^0-9]/', '', $valNoDec);
                        }

                        $year = (int) date('Y');
                        if ($monthNum >= 7 && $monthNum <= 12) {
                            // Penyesuaian tahun kepengurusan
                        }

                        if ($amount > 0) {
                            MonthlyDue::updateOrCreate(
                                ['user_id' => $user->id, 'month' => $monthNum, 'year' => $year],
                                ['amount' => $amount]
                            );
                        } else {
                            MonthlyDue::where('user_id', $user->id)
                                ->where('month', $monthNum)
                                ->where('year', $year)
                                ->delete();
                        }
                    }
                }
            });

            $msg = "Berhasil: $successCount pengurus disinkronkan.";
            if (count($unmatchedIds) > 0) $msg .= " Peringatan: ID tidak valid pada " . count($unmatchedIds) . " baris (" . implode(', ', array_slice($unmatchedIds, 0, 3)) . "...).";

            return response()->json(['message' => $msg]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyinkronisasi data kas.', 'error' => $e->getMessage()], 500);
        }
    }
}
