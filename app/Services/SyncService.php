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

    private function findColIndex(array $header, array $keywords): int|false
    {
        foreach ($header as $index => $colName) {
            $cleanColName = strtolower(trim($colName));
            foreach ($keywords as $keyword) {
                if (str_contains($cleanColName, strtolower(trim($keyword)))) {
                    return $index;
                }
            }
        }
        return false;
    }

    private function extractVal(array $row, int|bool $index): ?string
    {
        if ($index === false || !isset($row[$index])) return null;
        $val = trim($row[$index]);
        return (strtolower($val) === 'nan' || $val === '') ? null : $val;
    }

    public function syncMonthlyDues(string $url): array
    {
        $rows = $this->fetchCsv($url);
        $idIdx = false;
        $dataStartIndex = 0;
        $bulanMap = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12
        ];
        $bulanIndexes = [];

        foreach ($rows as $index => $row) {
            $cleanRow = array_map(fn($v) => strtolower(trim($v)), $row);
            
            if ($idIdx === false) {
                $idIdx = $this->findColIndex($cleanRow, ['id user', 'id anggota']);
            }

            if ($this->findColIndex($cleanRow, ['oktober', 'januari']) !== false) {
                $dataStartIndex = $index + 1;
                foreach ($bulanMap as $namaBulan => $angkaBulan) {
                    $idx = $this->findColIndex($cleanRow, [$namaBulan]);
                    if ($idx !== false) {
                        $bulanIndexes[$namaBulan] = ['index' => $idx, 'month_num' => $angkaBulan];
                    }
                }
                break;
            }
        }

        if (empty($bulanIndexes) || $idIdx === false) throw new Exception('Format Header Kas tidak ditemukan.');

        $successCount = 0;
        DB::transaction(function () use ($rows, $dataStartIndex, $idIdx, $bulanIndexes, &$successCount) {
            for ($i = $dataStartIndex; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty($row) || count($row) < 3) continue;

                $userId = $this->extractVal($row, $idIdx);
                if (!$userId || !is_numeric($userId)) continue;

                $user = User::find($userId);
                if (!$user) continue;

                $successCount++;
                foreach ($bulanIndexes as $bData) {
                    $valRaw = $this->extractVal($row, $bData['index']);
                    $amount = $valRaw ? (float) preg_replace('/[^0-9]/', '', explode(',', $valRaw)[0]) : 0;
                    
                    if ($amount > 0) {
                        MonthlyDue::updateOrCreate(
                            ['user_id' => $user->id, 'month' => $bData['month_num'], 'year' => (int) date('Y')], 
                            ['amount' => $amount]
                        );
                    } else {
                        MonthlyDue::where('user_id', $user->id)
                            ->where('month', $bData['month_num'])
                            ->where('year', (int) date('Y'))
                            ->delete();
                    }
                }
            }
        });

        return ['message' => "Berhasil: $successCount pengurus disinkronkan."];
    }

    public function syncAgendas(?int $eventId, string $url): array
    {
        $rows = $this->fetchCsv($url);
        $header = [];
        $dataStartIndex = 0;
        
        foreach ($rows as $index => $row) {
            $cleanRow = array_map(fn($v) => strtolower(trim($v)), $row);
            if ($this->findColIndex($cleanRow, ['nama agenda']) !== false && $this->findColIndex($cleanRow, ['tanggal mulai', 'mulai']) !== false) {
                $header = $cleanRow;
                $dataStartIndex = $index + 1;
                break;
            }
        }

        if (empty($header)) throw new Exception('Format Header Agenda tidak ditemukan.');

        $idx = [
            'nama'      => $this->findColIndex($header, ['nama agenda', 'kegiatan']),
            'start'     => $this->findColIndex($header, ['tanggal mulai', 'waktu mulai']),
            'end'       => $this->findColIndex($header, ['tanggal selesai', 'waktu selesai']),
            'tempat'    => $this->findColIndex($header, ['tempat', 'lokasi']),
            'pj'        => $this->findColIndex($header, ['pj/divisi', 'pj', 'penanggungjawab', 'pic']),
            'status'    => $this->findColIndex($header, ['status', 'keterangan']),
            'notulensi' => $this->findColIndex($header, ['link notulensi', 'notulensi']),
        ];

        $parseDate = fn ($d) => $d ? (Carbon::parse(str_replace('/', '-', $d))->format('Y-m-d H:i:s') ?: null) : null;

        $successCount = 0;
        DB::transaction(function () use ($rows, $dataStartIndex, $idx, $parseDate, $eventId, &$successCount) {
            for ($i = $dataStartIndex; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty($row) || count($row) < 3) continue;

                $nama  = $this->extractVal($row, $idx['nama']);
                $start = $parseDate($this->extractVal($row, $idx['start']));
                if (!$nama || !$start) continue;

                $agenda = Agenda::firstOrNew([
                    'event_id'   => $eventId ? (int)$eventId : null,
                    'title'      => $nama,
                    'start_date' => $start,
                ]);

                $agenda->end_date    = $parseDate($this->extractVal($row, $idx['end'])) ?? $agenda->end_date;
                $agenda->location    = $this->extractVal($row, $idx['tempat']) ?? $agenda->location;
                $agenda->pic         = $this->extractVal($row, $idx['pj']) ?? $agenda->pic;
                $agenda->status      = $this->extractVal($row, $idx['status']) ?? $agenda->status;
                
                $urlVal = $this->extractVal($row, $idx['notulensi']);
                $parsedUrl = $urlVal ? filter_var($urlVal, FILTER_VALIDATE_URL) : false;
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
            $cleanRow = array_map(fn($v) => strtolower(trim($v)), $row);
            if ($this->findColIndex($cleanRow, ['nomor surat']) !== false && $this->findColIndex($cleanRow, ['perihal']) !== false) {
                $header = $cleanRow;
                $dataStartIndex = $index + 1;
                break;
            }
        }

        if (empty($header)) throw new Exception('Format Header Dokumen tidak ditemukan.');

        $idx = [
            'noSurat'     => $this->findColIndex($header, ['nomor surat', 'no surat']),
            'perihal'     => $this->findColIndex($header, ['perihal', 'judul']),
            'tglBuat'     => $this->findColIndex($header, ['tanggal dibuat', 'tgl buat']),
            'tglKegiatan' => $this->findColIndex($header, ['tanggal kegiatan', 'tgl kegiatan', 'pelaksanaan']),
            'linkSurat'   => $this->findColIndex($header, ['link surat', 'draft']),
            'linkScan'    => $this->findColIndex($header, ['link scan surat', 'link scan', 'scan']),
        ];

        $parseDate = function ($dateStr) {
            try { return $dateStr ? Carbon::parse(str_replace('/', '-', $dateStr))->format('Y-m-d') : null; } 
            catch (\Exception $e) { return null; }
        };

        $success = 0;
        DB::transaction(function () use ($rows, $dataStartIndex, $idx, $parseDate, $eventId, $userId, &$success) {
            for ($i = $dataStartIndex; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty($row) || count($row) < 3) continue;

                $noSurat = $this->extractVal($row, $idx['noSurat']);
                if (!$noSurat) continue;

                $perihal = $this->extractVal($row, $idx['perihal']);
                $tglBuat = $parseDate($this->extractVal($row, $idx['tglBuat'])) ?? now()->toDateString();

                $doc = Document::firstOrNew([
                    'letter_number' => $noSurat,
                    'event_id'      => $eventId ? (int)$eventId : null,
                ]);

                $doc->title = $perihal ?? $doc->title ?? 'Tanpa Judul';
                
                $linkSuratVal = $this->extractVal($row, $idx['linkSurat']);
                $doc->letter_link = ($linkSuratVal && filter_var($linkSuratVal, FILTER_VALIDATE_URL)) ? $linkSuratVal : $doc->letter_link;
                
                $linkScanVal = $this->extractVal($row, $idx['linkScan']);
                $doc->scan_link = ($linkScanVal && filter_var($linkScanVal, FILTER_VALIDATE_URL)) ? $linkScanVal : $doc->scan_link;
                
                // NULLIFIER CORRECTION: 
                // Cabut Smart Upsert (?? $doc->activity_date). Paksa menjadi NULL jika di Spreadsheet kosong!
                $doc->activity_date = $parseDate($this->extractVal($row, $idx['tglKegiatan']));
                
                $doc->created_by = $doc->exists ? $doc->created_by : $userId;

                $doc->timestamps = false;
                $doc->created_at = $tglBuat . ' 00:00:00';
                $doc->save();
                $doc->timestamps = true;

                $success++;
            }
        });

        return ['message' => "Sinkronisasi selesai. Berhasil: $success surat."];
    }

    public function syncFinances(?int $eventId, string $url, int $userId): array
    {
        $rows = $this->fetchCsv($url);
        $header = [];
        $dataStartIndex = 0;

        foreach ($rows as $index => $row) {
            $cleanRow = array_map(fn($v) => strtolower(trim($v)), $row);
            if ($this->findColIndex($cleanRow, ['tipe']) !== false && $this->findColIndex($cleanRow, ['rincian']) !== false) {
                $header = $cleanRow;
                $dataStartIndex = $index + 1;
                break;
            }
        }

        if (empty($header)) throw new Exception('Format Header Keuangan tidak ditemukan.');

        $idx = [
            'tgl'      => $this->findColIndex($header, ['tanggal (yyyy-mm-dd)', 'tanggal', 'tgl']),
            'tipe'     => $this->findColIndex($header, ['tipe', 'jenis']),
            'rincian'  => $this->findColIndex($header, ['rincian', 'deskripsi', 'item']),
            'kategori' => $this->findColIndex($header, ['kategori', 'kelompok']),
            'vol'      => $this->findColIndex($header, ['volume', 'qty', 'jumlah']),
            'satuan'   => $this->findColIndex($header, ['satuan', 'unit']),
            'harga'    => $this->findColIndex($header, ['harga satuan', 'harga', 'unit price']),
            'sumber'   => $this->findColIndex($header, ['sumber dana', 'sumber']),
            'pic'      => $this->findColIndex($header, ['penanggungjawab', 'pic', 'pj']),
            'metode'   => $this->findColIndex($header, ['metode', 'pembayaran']),
            'nota'     => $this->findColIndex($header, ['link nota', 'nota', 'bukti']),
            'ket'      => $this->findColIndex($header, ['keterangan', 'ket', 'catatan']),
        ];

        $parseDate = fn ($d) => $d ? (Carbon::parse(str_replace('/', '-', $d))->format('Y-m-d') ?: null) : null;
        $parsePrice = fn ($p) => (float) preg_replace('/[^0-9]/', '', explode(',', $p)[0] ?? '0');

        DB::transaction(function () use ($rows, $dataStartIndex, $idx, $parseDate, $parsePrice, $eventId, $userId) {
            for ($i = $dataStartIndex; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty($row) || count($row) < 3) continue;

                $rincian = $this->extractVal($row, $idx['rincian']);
                $tipeRaw = $this->extractVal($row, $idx['tipe']);
                if (!$rincian || !$tipeRaw) continue;

                $type = (stripos($tipeRaw, 'masuk') !== false || strtolower($tipeRaw) === 'income') ? 'income' : 'expense';
                $qtyStr = $this->extractVal($row, $idx['vol']);
                $qty = $qtyStr ? (float)$qtyStr : 1;
                
                $priceStr = $this->extractVal($row, $idx['harga']);
                $price = $priceStr ? $parsePrice($priceStr) : 0;
                
                $tgl = $parseDate($this->extractVal($row, $idx['tgl'])) ?? now()->toDateString();

                $finance = Finance::firstOrNew([
                    'event_id' => $eventId ? (int)$eventId : null,
                    'type'     => $type,
                    'title'    => $rincian,
                    'date'     => $tgl,
                ]);

                $finance->user_id        = $finance->exists ? $finance->user_id : $userId;
                $finance->description    = $rincian;
                $finance->qty            = $qty;
                $finance->unit           = $this->extractVal($row, $idx['satuan']) ?? $finance->unit;
                $finance->unit_price     = $price;
                $finance->amount         = $qty * $price;
                $finance->category       = $this->extractVal($row, $idx['kategori']) ?? $finance->category;
                $finance->funding_source = $this->extractVal($row, $idx['sumber']) ?? $finance->funding_source;
                $finance->pic            = $this->extractVal($row, $idx['pic']) ?? $finance->pic;
                $finance->payment_method = $this->extractVal($row, $idx['metode']) ?? $finance->payment_method;
                $finance->notes          = $this->extractVal($row, $idx['ket']) ?? $finance->notes;
                
                $urlVal = $this->extractVal($row, $idx['nota']);
                $parsedUrl = $urlVal ? filter_var($urlVal, FILTER_VALIDATE_URL) : false;
                $finance->receipt_url    = $parsedUrl ? $parsedUrl : $finance->receipt_url;

                $finance->save();
            }
        });

        return ['message' => "Sinkronisasi berhasil."];
    }
}
