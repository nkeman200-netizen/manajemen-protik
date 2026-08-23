<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DocumentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $documents = Document::with(['creator', 'event'])
            ->when($request->search, fn ($q, $search) =>
                $q->where(fn ($q) =>
                    $q->where('letter_number', 'like', "%{$search}%")
                      ->orWhere('title', 'like', "%{$search}%")
                )
            )
            ->where('event_id', $request->input('event_id'))
            ->latest()
            ->paginate(15);

        return DocumentResource::collection($documents);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeEventAccess($request->user(), $request->event_id, ['Ketua', 'Sekretaris']);

        $validated = $request->validate([
            'created_by'    => ['required', 'exists:users,id'],
            'event_id'      => ['nullable', 'exists:events,id'],
            'letter_number' => ['required', 'string', 'max:255', 'unique:documents,letter_number'],
            'title'         => ['required', 'string', 'max:255'],
            'letter_link'   => ['nullable', 'string', 'max:255'],
            'scan_link'     => ['nullable', 'string', 'max:255'],
            'activity_date' => ['nullable', 'date'],
        ]);

        $document = Document::create($validated);

        return response()->json(['message' => 'Success', 'data' => new DocumentResource($document)], 201);
    }

    public function update(Request $request, Document $document): JsonResponse
    {
        $this->authorizeEventAccess($request->user(), $document->event_id, ['Ketua', 'Sekretaris']);

        $validated = $request->validate([
            'event_id'      => ['nullable', 'exists:events,id'],
            'letter_number' => ['required', 'string', 'max:255', 'unique:documents,letter_number,' . $document->id],
            'title'         => ['required', 'string', 'max:255'],
            'letter_link'   => ['nullable', 'string', 'max:255'],
            'scan_link'     => ['nullable', 'string', 'max:255'],
            'activity_date' => ['nullable', 'date'],
        ]);

        $document->update($validated);

        return response()->json([
            'message' => 'Success',
            'data'    => new DocumentResource($document->load(['creator', 'event'])),
        ]);
    }

    public function destroy(Request $request, Document $document): JsonResponse
    {
        $this->authorizeEventAccess($request->user(), $document->event_id, ['Ketua', 'Sekretaris']);

        $document->delete();

        return response()->json([
            'message' => 'Success',
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $eventId = $request->input('event_id');

        if ($eventId) {
            $event = Event::find($eventId);
            if (!$event || empty($event->document_sync_url)) {
                return response()->json(['message' => 'URL Sinkronisasi Dokumen untuk Event ini belum diatur.'], 400);
            }
            $url = $event->document_sync_url;
        } else {
            $url = env('TRACKING_PERSURATAN_URL');
            if (!$url) return response()->json(['message' => 'URL Sinkronisasi Dokumen BPH Pusat belum dikonfigurasi.'], 500);
        }

        $separator = str_contains($url, '?') ? '&' : '?';
        $freshUrl = $url . $separator . 'cb=' . time();

        try {
            $context = stream_context_create(['http' => ['header' => "Cache-Control: no-cache\r\n"]]);
            $csvData = file_get_contents($freshUrl, false, $context);
            $rows = array_map('str_getcsv', explode("\n", $csvData));
            
            $header = [];
            $dataStartIndex = 0;
            
            // FIX: Sanitasi Header menggunakan trim
            foreach ($rows as $index => $row) {
                $cleanRow = array_map('trim', $row);
                if (in_array('Nomor Surat', $cleanRow) && in_array('Perihal', $cleanRow)) {
                    $header = $cleanRow;
                    $dataStartIndex = $index + 1;
                    break;
                }
            }

            if (empty($header)) return response()->json(['message' => 'Format Header (Nomor Surat & Perihal) tidak ditemukan.'], 400);

            $noSuratIdx = array_search('Nomor Surat', $header);
            $perihalIdx = array_search('Perihal', $header);
            $keteranganIdx = array_search('Keterangan', $header);
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
                // FIX: Sanitasi seluruh isi baris untuk membersihkan spasi tak kasat mata
                $row = array_map('trim', $rows[$i]);
                if (empty($row) || count($row) < 3) continue;

                $noSurat = $row[$noSuratIdx] ?? null;
                $perihal = $row[$perihalIdx] ?? null;
                
                // FIX: Validasi ketat terhadap spasi kosong
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
                            'created_by'    => auth()->id() ?? 1,
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

            return response()->json(['message' => "Sinkronisasi selesai. Berhasil: $success surat. Gagal/Dilewati: $failed surat."]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyinkronisasi data.', 'error' => $e->getMessage()], 500);
        }
    }
}
