<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocumentResource;
use App\Models\Document;
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
            'drive_url'     => ['required', 'string', 'max:255'],
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
            'drive_url'     => ['required', 'string', 'max:255'],
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
        $url = env('TRACKING_PERSURATAN_URL');
        
        if (!$url) {
            return response()->json(['message' => 'URL Sinkronisasi (TRACKING_PERSURATAN_URL) belum dikonfigurasi di .env'], 500);
        }

        try {
            $csvData = file_get_contents($url);
            $rows = array_map('str_getcsv', explode("\n", $csvData));
            
            // Mencari baris header (Mencari 'Nomor Surat' dan 'Perihal')
            $header = [];
            $dataStartIndex = 0;
            foreach ($rows as $index => $row) {
                if (in_array('Nomor Surat', $row) && in_array('Perihal', $row)) {
                    $header = $row;
                    $dataStartIndex = $index + 1;
                    break;
                }
            }

            if (empty($header)) {
                return response()->json(['message' => 'Format Header (Nomor Surat & Perihal) tidak ditemukan pada dokumen sumber.'], 400);
            }

            $noSuratIdx = array_search('Nomor Surat', $header);
            $perihalIdx = array_search('Perihal', $header);
            $keteranganIdx = array_search('Keterangan', $header);
            $linkSuratIdx = array_search('Link Surat', $header);
            $linkScanIdx = array_search('Link Scan Surat', $header);

            $success = 0;
            $failed = 0;

            for ($i = $dataStartIndex; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty($row) || count($row) < 3) continue;

                $noSurat = $row[$noSuratIdx] ?? null;
                $perihal = $row[$perihalIdx] ?? null;
                $keterangan = ($keteranganIdx !== false) ? ($row[$keteranganIdx] ?? null) : null;
                
                $linkScan = ($linkScanIdx !== false) ? ($row[$linkScanIdx] ?? null) : null;
                $linkSurat = ($linkSuratIdx !== false) ? ($row[$linkSuratIdx] ?? null) : null;
                
                // Fallback Link: Jika Link Scan kosong, pakai Link Surat. Jika kosong juga, pakai '#'
                $link = (!empty($linkScan) && strtolower($linkScan) !== 'nan') 
                    ? $linkScan 
                    : ((!empty($linkSurat) && strtolower($linkSurat) !== 'nan') ? $linkSurat : '#');

                if (empty($noSurat) || strtolower($noSurat) === 'nan') continue;

                // Fallback Title: Jika Perihal kosong, pakai Keterangan
                $title = (!empty($perihal) && strtolower($perihal) !== 'nan') 
                    ? $perihal 
                    : ((!empty($keterangan) && strtolower($keterangan) !== 'nan') ? $keterangan : 'Tanpa Judul');

                try {
                    Document::updateOrCreate(
                        ['letter_number' => $noSurat],
                        [
                            'title'      => $title,
                            'drive_url'  => $link,
                            'event_id'   => null, // Tersentralisasi
                            'created_by' => auth()->id() ?? 1,
                        ]
                    );
                    $success++;
                } catch (\Exception $e) {
                    $failed++;
                }
            }

            return response()->json([
                'message' => "Sinkronisasi selesai. Berhasil: $success surat. Gagal/Dilewati: $failed surat."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengunduh atau membaca data dari Google Sheets.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
