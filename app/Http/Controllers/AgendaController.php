<?php

namespace App\Http\Controllers;

use App\Http\Resources\AgendaResource;
use App\Models\Agenda;
use App\Models\AgendaTarget;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class AgendaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $eventId = $request->input('event_id');
        $search  = $request->input('search');

        $agendas = Agenda::with(['attendances', 'targets'])
            ->when($eventId, fn($q) => $q->where('event_id', $eventId), fn($q) => $q->whereNull('event_id'))
            ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
            ->orderBy('start_date', 'asc')
            ->paginate(15);

        return AgendaResource::collection($agendas);
    }

    public function sync(Request $request): JsonResponse
    {
        $eventId = $request->input('event_id');

        // Untuk Agenda, asumsikan URL berada di .env (BPH) atau event_sync_url (opsional ke depan). 
        // Sementara kita pakai TRACKING_AGENDA_URL dari env
        $url = env('TRACKING_AGENDA_URL');
        if (!$url) return response()->json(['message' => 'URL Sinkronisasi Agenda belum dikonfigurasi di .env'], 500);

        $separator = str_contains($url, '?') ? '&' : '?';
        $freshUrl = $url . $separator . 'cb=' . time();

        try {
            $context = stream_context_create(['http' => ['header' => "Cache-Control: no-cache\r\n"]]);
            $csvData = file_get_contents($freshUrl, false, $context);
            $rows = array_map('str_getcsv', explode("\n", $csvData));
            
            $header = [];
            $dataStartIndex = 0;
            
            // Pencarian Header Agnostik
            foreach ($rows as $index => $row) {
                $cleanRow = array_map('trim', $row);
                $rowString = strtolower(implode(' | ', $cleanRow));
                
                if (str_contains($rowString, 'nama agenda') && str_contains($rowString, 'tanggal mulai')) {
                    $header = $cleanRow;
                    $dataStartIndex = $index + 1;
                    break;
                }
            }

            if (empty($header)) return response()->json(['message' => 'Format Header (Nama Agenda, Tanggal Mulai, dll) tidak ditemukan.'], 400);

            $idx = [
                'nama'      => array_search('Nama Agenda', $header),
                'start'     => array_search('Tanggal Mulai', $header),
                'end'       => array_search('Tanggal Selesai', $header),
                'tempat'    => array_search('Tempat', $header),
                'pj'        => array_search('PJ/Divisi', $header),
                'status'    => array_search('Status', $header),
                'notulensi' => array_search('Link Notulensi', $header),
            ];

            $parseDate = function ($dateStr) {
                if (empty($dateStr) || strtolower(trim($dateStr)) === 'nat' || strtolower(trim($dateStr)) === 'nan') return null;
                try { 
                    // FIX UTAMA: Ubah garis miring (/) menjadi strip (-) agar dikenali sebagai format DD-MM-YYYY oleh PHP/Carbon
                    $cleanDate = str_replace('/', '-', trim($dateStr));
                    return Carbon::parse($cleanDate)->format('Y-m-d H:i:s'); 
                } catch (\Exception $e) { 
                    return null; 
                }
            };
            
            $parseUrl = function ($urlStr) { return filter_var(trim($urlStr), FILTER_VALIDATE_URL) ? trim($urlStr) : null; };
            $val = function($row, $index) { if ($index === false || !isset($row[$index])) return null; $v = trim($row[$index]); return (strtolower($v) === 'nan' || $v === '') ? null : $v; };

            $successCount = 0;

            DB::transaction(function () use ($rows, $dataStartIndex, $idx, $parseDate, $parseUrl, $val, $eventId, &$successCount) {
                // Wipe Data (Scope Event/Global)
                if ($eventId) {
                    Agenda::where('event_id', $eventId)->delete();
                } else {
                    Agenda::whereNull('event_id')->delete();
                }

                for ($i = $dataStartIndex; $i < count($rows); $i++) {
                    $row = array_map('trim', $rows[$i]);
                    if (empty($row) || count($row) < 3) continue;

                    $nama  = $val($row, $idx['nama']);
                    $start = $parseDate($val($row, $idx['start']));
                    
                    if (empty($nama) || !$start) continue;

                    Agenda::create([
                        'event_id'    => $eventId ? (int)$eventId : null,
                        'title'       => $nama,
                        'start_date'  => $start,
                        'end_date'    => $parseDate($val($row, $idx['end'])),
                        'location'    => $val($row, $idx['tempat']),
                        'pic'         => $val($row, $idx['pj']),
                        'status'      => $val($row, $idx['status']),
                        'minutes_url' => $parseUrl($val($row, $idx['notulensi'])),
                    ]);

                    $successCount++;
                }
            });

            $target = $eventId ? "Kepanitiaan" : "BPH Pusat";
            return response()->json(['message' => "Sinkronisasi selesai. Berhasil menyinkronkan $successCount agenda $target."]);

        } catch (\Exception $e) { return response()->json(['message' => 'Gagal menyinkronisasi data agenda.', 'error' => $e->getMessage()], 500); }
    }

    public function setTargets(Request $request, $id): JsonResponse
    {
        $agenda = Agenda::findOrFail($id);
        
        $validated = $request->validate([
            'targets'          => 'required|array',
            'targets.*.type'   => 'required|in:all,bph,coordinator,division,position,user',
            'targets.*.value'  => 'nullable|string',
        ]);

        DB::transaction(function () use ($agenda, $validated) {
            // Hapus target lama
            $agenda->targets()->delete();
            
            // Masukkan target baru
            foreach ($validated['targets'] as $t) {
                AgendaTarget::create([
                    'agenda_id'    => $agenda->id,
                    'target_type'  => $t['type'],
                    'target_value' => $t['value'],
                ]);
            }
        });

        return response()->json(['message' => 'Target peserta agenda berhasil diperbarui.', 'data' => $agenda->targets]);
    }
}
