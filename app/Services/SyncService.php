<?php

namespace App\Services;

use App\Models\Agenda;
use App\Models\Document;
use App\Models\Finance;
use App\Models\MonthlyDue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class SyncService
{
    private function fetchCsv(string $url): array
    {
        $separator = str_contains($url, '?') ? '&' : '?';
        $freshUrl = $url . $separator . 'cb=' . time();
        $context = stream_context_create(['http' => ['header' => "Cache-Control: no-cache\r\n"]]);
        $csvData = file_get_contents($freshUrl, false, $context);
        
        if (!$csvData) throw new Exception("Gagal mengunduh data dari URL Spreadsheet.");
        
        return array_map('str_getcsv', explode("\n", $csvData));
    }

    public function syncMonthlyDues(string $url): array
    {
        $rows = $this->fetchCsv($url);
        $idIdx = false;
        $dataStartIndex = 0;
        $bulanMap = [
            'JANUARI' => 1, 'FEBRUARI' => 2, 'MARET' => 3, 'APRIL' => 4,
            'MEI' => 5, 'JUNI' => 6, 'JULI' => 7, 'AGUSTUS' => 8,
            'SEPTEMBER' => 9, 'OKTOBER' => 10, 'NOVEMBER' => 11, 'DESEMBER' => 12
        ];
        $bulanIndexes = [];

        foreach ($rows as $index => $row) {
            $cleanRow = array_map(fn($v) => strtoupper(trim($v)), $row);
            if (in_array('ID USER', $cleanRow)) $idIdx = array_search('ID USER', $cleanRow);
            if (in_array('OKTOBER', $cleanRow)) {
                $dataStartIndex = $index + 1;
                foreach ($bulanMap as $namaBulan => $angkaBulan) {
                    $idx = array_search($namaBulan, $cleanRow);
                    if ($idx !== false) $bulanIndexes[$namaBulan] = ['index' => $idx, 'month_num' => $angkaBulan];
                }
                break;
            }
        }

        if (empty($bulanIndexes) || $idIdx === false) throw new Exception('Format Header (ID USER & OKTOBER) tidak ditemukan.');

        $successCount = 0;
        $unmatchedIds = [];

        DB::transaction(function () use ($rows, $dataStartIndex, $idIdx, $bulanIndexes, &$successCount, &$unmatchedIds) {
            for ($i = $dataStartIndex; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty($row) || count($row) < 3) continue;

                $userId = trim($row[$idIdx] ?? '');
                if (empty($userId) || strtolower($userId) === 'nan' || !is_numeric($userId)) continue;

                $user = User::find($userId);
                if (!$user) {
                    $unmatchedIds[] = "Baris " . ($i + 1) . " (ID: $userId)";
                    continue;
                }

                $successCount++;
                foreach ($bulanIndexes as $bData) {
                    $valRaw = trim($row[$bData['index']] ?? ''); 
                    $amount = (strtolower($valRaw) === 'nan' || $valRaw === '') ? 0 : (float) preg_replace('/[^0-9]/', '', explode(',', $valRaw)[0]);
                    
                    if ($amount > 0) {
                        MonthlyDue::updateOrCreate(['user_id' => $user->id, 'month' => $bData['month_num'], 'year' => (int) date('Y')], ['amount' => $amount]);
                    } else {
                        MonthlyDue::where('user_id', $user->id)->where('month', $bData['month_num'])->where('year', (int) date('Y'))->delete();
                    }
                }
            }
        });

        $msg = "Berhasil: $successCount pengurus disinkronkan.";
        if (count($unmatchedIds) > 0) $msg .= " Peringatan: ID tidak valid pada " . count($unmatchedIds) . " baris.";
        return ['message' => $msg];
    }

    public function syncAgendas(?int $eventId, string $url): array
    {
        $rows = $this->fetchCsv($url);
        $header = [];
        $dataStartIndex = 0;
        
        foreach ($rows as $index => $row) {
            $cleanRow = array_map('trim', $row);
            $rowString = strtolower(implode(' | ', $cleanRow));
            if (str_contains($rowString, 'nama agenda') && str_contains($rowString, 'tanggal mulai')) {
                $header = $cleanRow;
                $dataStartIndex = $index + 1;
                break;
            }
        }

        if (empty($header)) throw new Exception('Format Header (Nama Agenda, Tanggal Mulai) tidak ditemukan.');

        $idx = [
            'nama'      => array_search('Nama Agenda', $header),
            'start'     => array_search('Tanggal Mulai', $header),
            'end'       => array_search('Tanggal Selesai', $header),
            'tempat'    => array_search('Tempat', $header),
            'pj'        => array_search('PJ/Divisi', $header),
            'status'    => array_search('Status', $header),
            'notulensi' => array_search('Link Notulensi', $header),
        ];

        $parseDate = fn ($d) => (empty($d) || strtolower(trim($d)) === 'nat' || strtolower(trim($d)) === 'nan') ? null : (Carbon::parse(str_replace('/', '-', trim($d)))->format('Y-m-d H:i:s') ?: null);
        $val = fn($row, $index) => ($index === false || !isset($row[$index]) || strtolower(trim($row[$index])) === 'nan' || trim($row[$index]) === '') ? null : trim($row[$index]);

        $successCount = 0;
        DB::transaction(function () use ($rows, $dataStartIndex, $idx, $parseDate, $val, $eventId, &$successCount) {
            // HAPUS Logika Wipe & Reload agar ID tidak berubah
            // $eventId ? Agenda::where('event_id', $eventId)->delete() : Agenda::whereNull('event_id')->delete();

            for ($i = $dataStartIndex; $i < count($rows); $i++) {
                $row = array_map('trim', $rows[$i]);
                if (empty($row) || count($row) < 3) continue;

                $nama  = $val($row, $idx['nama']);
                $start = $parseDate($val($row, $idx['start']));
                if (empty($nama) || !$start) continue;

                // 1. SMART UPSERT: Cari data berdasarkan Nama + Tanggal Mulai + Event ID
                $agenda = Agenda::firstOrNew([
                    'event_id'   => $eventId ? (int)$eventId : null,
                    'title'      => $nama,
                    'start_date' => $start,
                ]);

                // 2. UPDATE SELECTIVE: Gunakan data CSV jika ada. Jika CSV kosong, pertahankan data DB.
                $agenda->end_date    = $parseDate($val($row, $idx['end'])) ?? $agenda->end_date;
                $agenda->location    = $val($row, $idx['tempat']) ?? $agenda->location;
                $agenda->pic         = $val($row, $idx['pj']) ?? $agenda->pic;
                $agenda->status      = $val($row, $idx['status']) ?? $agenda->status;
                
                $parsedUrl = filter_var($val($row, $idx['notulensi']), FILTER_VALIDATE_URL);
                $agenda->minutes_url = $parsedUrl ? $parsedUrl : $agenda->minutes_url;

                $agenda->save();
                $successCount++;
            }
        });

        return ['message' => "Sinkronisasi selesai. Berhasil menyinkronkan $successCount agenda."];
    }

    public function syncDocuments(?int $eventId, string $url, int $userId): array
    {
        $rows = $this->fetchCsv($url);
        $header = [];
        $dataStartIndex = 0;

        foreach ($rows as $index => $row) {
            $cleanRow = array_map('trim', $row);
            if (in_array('Nomor Surat', $cleanRow) && in_array('Perihal', $cleanRow)) {
                $header = $cleanRow;
                $dataStartIndex = $index + 1;
                break;
            }
        }

        if (empty($header)) throw new Exception('Format Header (Nomor Surat & Perihal) tidak ditemukan.');

        $noSuratIdx = array_search('Nomor Surat', $header);
        $perihalIdx = array_search('Perihal', $header);
        $tglBuatIdx = array_search('Tanggal Dibuat', $header);
        $tglKegiatanIdx = array_search('Tanggal Kegiatan', $header);
        $linkSuratIdx = array_search('Link Surat', $header);
        $linkScanIdx = array_search('Link Scan Surat', $header);

        $parseDate = function ($dateStr) {
            try {
                return Carbon::parse(str_replace('/', '-', trim($dateStr)))->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        };
        $parseUrl = function ($urlStr) {
            return filter_var(trim($urlStr), FILTER_VALIDATE_URL) ? trim($urlStr) : null;
        };

        $success = 0;
        $failed = 0;

        for ($i = $dataStartIndex; $i < count($rows); $i++) {
            $row = array_map('trim', $rows[$i]);
            if (empty($row) || count($row) < 3) continue;

            $noSurat = $row[$noSuratIdx] ?? null;
            $perihal = $row[$perihalIdx] ?? null;
            
            if (empty($noSurat) || strtolower($noSurat) === 'nan') continue;

            $tglBuat = $parseDate(($tglBuatIdx !== false) ? ($row[$tglBuatIdx] ?? null) : null) ?? now()->toDateString();
            $title = (!empty($perihal) && strtolower($perihal) !== 'nan') ? $perihal : 'Tanpa Judul';

            try {
                $doc = Document::updateOrCreate(
                    [
                        'letter_number' => $noSurat,
                        'event_id'      => $eventId ? (int)$eventId : null,
                    ],
                    [
                        'title'         => $title,
                        'letter_link'   => $parseUrl(($linkSuratIdx !== false) ? ($row[$linkSuratIdx] ?? null) : null),
                        'scan_link'     => $parseUrl(($linkScanIdx !== false) ? ($row[$linkScanIdx] ?? null) : null),
                        'activity_date' => $parseDate(($tglKegiatanIdx !== false) ? ($row[$tglKegiatanIdx] ?? null) : null),
                        'created_by'    => $userId,
                    ]
                );

                $doc->timestamps = false;
                $doc->created_at = $tglBuat . ' 00:00:00';
                $doc->save();
                $doc->timestamps = true;

                $success++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        return ['message' => "Sinkronisasi selesai. Berhasil: $success surat. Gagal/Dilewati: $failed surat."];
    }

    public function syncFinances(?int $eventId, string $url, int $userId): array
    {
        $rows = $this->fetchCsv($url);
        $header = [];
        $dataStartIndex = 0;

        foreach ($rows as $index => $row) {
            $cleanRow = array_map('trim', $row);
            $rowString = strtolower(implode(' | ', $cleanRow));
            if (str_contains($rowString, 'tipe') && str_contains($rowString, 'rincian')) {
                $header = $cleanRow;
                $dataStartIndex = $index + 1;
                break;
            }
        }

        if (empty($header)) throw new Exception('Format Header tidak ditemukan.');

        $idx = [
            'tgl'      => array_search('Tanggal (YYYY-MM-DD)', $header) ?: array_search('Tanggal', $header),
            'tipe'     => array_search('Tipe (Pemasukan/Pengeluaran)', $header) ?: array_search('Tipe', $header),
            'rincian'  => array_search('Rincian', $header),
            'kategori' => array_search('Kategori', $header),
            'vol'      => array_search('Volume', $header),
            'satuan'   => array_search('Satuan', $header),
            'harga'    => array_search('Harga Satuan', $header),
            'sumber'   => array_search('Sumber Dana', $header),
            'pic'      => array_search('Penanggungjawab', $header),
            'metode'   => array_search('Metode', $header),
            'nota'     => array_search('Link Nota', $header),
            'ket'      => array_search('Keterangan', $header),
        ];

        $parseDate = function ($dateStr) {
            try {
                return Carbon::parse(str_replace('/', '-', trim($dateStr)))->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        };
        $parseUrl = function ($urlStr) {
            return filter_var(trim($urlStr), FILTER_VALIDATE_URL) ? trim($urlStr) : null;
        };
        $parsePrice = function ($priceStr) {
            return (float) preg_replace('/[^0-9]/', '', explode(',', trim($priceStr))[0] ?? '0');
        };
        $val = function($row, $index) {
            if ($index === false || !isset($row[$index])) return null;
            $v = trim($row[$index]);
            return (strtolower($v) === 'nan' || $v === '') ? null : $v;
        };

        DB::transaction(function () use ($rows, $dataStartIndex, $idx, $parseDate, $parseUrl, $parsePrice, $val, $eventId, $userId) {
            // HAPUS Logika Wipe & Reload
            // if ($eventId) { Finance::where('event_id', $eventId)->delete(); } else { Finance::whereNull('event_id')->delete(); }

            for ($i = $dataStartIndex; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty($row) || count($row) < 3) continue;

                $rincian = $val($row, $idx['rincian']);
                $tipeRaw = $val($row, $idx['tipe']);
                if (!$rincian || !$tipeRaw) continue;

                $type = (stripos($tipeRaw, 'masuk') !== false || strtolower($tipeRaw) === 'income') ? 'income' : 'expense';
                $qty = (float) ($val($row, $idx['vol']) ?? 1);
                $price = $parsePrice($val($row, $idx['harga']));
                $tgl = $parseDate($val($row, $idx['tgl'])) ?? now()->toDateString();

                // 1. SMART UPSERT: Gunakan Event ID, Tipe, Rincian, dan Tanggal sebagai Composite Key
                $finance = Finance::firstOrNew([
                    'event_id' => $eventId ? (int)$eventId : null,
                    'type'     => $type,
                    'title'    => $rincian,
                    'date'     => $tgl,
                ]);

                // 2. SELECTIVE UPDATE: Pertahankan data di DB jika CSV kosong
                $finance->user_id        = $finance->exists ? $finance->user_id : $userId;
                $finance->description    = $rincian;
                $finance->qty            = $qty;
                $finance->unit           = $val($row, $idx['satuan']) ?? $finance->unit;
                $finance->unit_price     = $price;
                $finance->amount         = $qty * $price;
                $finance->category       = $val($row, $idx['kategori']) ?? $finance->category;
                $finance->funding_source = $val($row, $idx['sumber']) ?? $finance->funding_source;
                $finance->pic            = $val($row, $idx['pic']) ?? $finance->pic;
                $finance->payment_method = $val($row, $idx['metode']) ?? $finance->payment_method;
                $finance->notes          = $val($row, $idx['ket']) ?? $finance->notes;
                
                $parsedUrl = $parseUrl($val($row, $idx['nota']));
                $finance->receipt_url    = $parsedUrl ? $parsedUrl : $finance->receipt_url;

                $finance->save();
            }
        });

        return ['message' => "Sinkronisasi berhasil."];
    }
}
