<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Models\Event;
use App\Models\Setting;
use App\Services\SyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DocumentController extends Controller
{
    public function __construct(private readonly SyncService $syncService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $documents = Document::with(['creator', 'event'])
            ->when($request->search, fn ($q, $search) =>
                $q->where(fn ($q) =>
                    $q->where('letter_number', 'like', "%{$search}%")
                      ->orWhere('title', 'like', "%{$search}%")
                      ->orWhere('origin', 'like', "%{$search}%")
                      ->orWhere('destination', 'like', "%{$search}%")
                )
            )
            ->when($request->type, fn ($q, $type) => $q->where('type', $type))
            ->when($request->classification_filter, fn ($q, $c) => $q->where('classification', $c))
            ->when($request->origin_filter, fn ($q, $o) => $q->where('origin', $o))
            ->when($request->destination_filter, fn ($q, $d) => $q->where('destination', $d))
            ->where('event_id', $request->input('event_id'))
            ->latest()
            ->paginate(15);

        return DocumentResource::collection($documents);
    }

    public function filters(Request $request): JsonResponse
    {
        $eventId = $request->query('event_id');
        $type    = $request->query('type', 'outgoing');

        $query = Document::when($eventId, fn($q) => $q->where('event_id', $eventId), fn($q) => $q->whereNull('event_id'))
            ->where('type', $type);

        return response()->json([
            'data' => [
                'classifications' => (clone $query)->whereNotNull('classification')->where('classification', '!=', '')->distinct()->pluck('classification'),
                'origins'         => (clone $query)->whereNotNull('origin')->where('origin', '!=', '')->distinct()->pluck('origin'),
                'destinations'    => (clone $query)->whereNotNull('destination')->where('destination', '!=', '')->distinct()->pluck('destination'),
            ]
        ]);
    }

    public function generateNumber(Request $request): JsonResponse
    {
        $type    = $request->query('type', 'peminjaman_perlengkapan');
        $eventId = $request->query('event_id');

        // 1. Ekstraksi nomor urut terakhir dalam tahun berjalan
        $currentYear = date('Y');
        $lastDoc     = Document::whereYear('created_at', $currentYear)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastDoc && $lastDoc->letter_number) {
            $parts = explode('/', $lastDoc->letter_number);
            if (isset($parts[0]) && is_numeric($parts[0])) {
                $nextNumber = (int) $parts[0] + 1;
            }
        }
        $urutan = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // 2. Kode jenis & rute surat
        $kodeJenis = 'SPm-i'; // Default: Surat Peminjaman internal
        if (str_contains($type, 'undangan_eksternal')) {
            $kodeJenis = 'SU-e';
        } elseif (str_contains($type, 'undangan_internal')) {
            $kodeJenis = 'SU-i';
        }

        // 3. Sisipan singkatan Event (opsional)
        $singkatan = '';
        if ($eventId) {
            $event = Event::find($eventId);
            if ($event && $event->abbreviation) {
                $cleanAbbr = strtoupper(str_replace(' ', '', $event->abbreviation));
                $singkatan = "PAN-{$cleanAbbr}/";
            }
        }

        // 4. Konversi bulan ke angka Romawi
        $romawi = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        $bulan  = $romawi[(int) date('n') - 1];

        // 5. Rakit string final
        $hasilNomor = "{$urutan}/{$kodeJenis}/{$singkatan}PROTIC/{$bulan}/{$currentYear}";

        return response()->json(['nomor_surat' => $hasilNomor]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeEventAccess($request->user(), $request->event_id, ['Ketua', 'Sekretaris']);

        $validated = $request->validate([
            'created_by'    => ['required', 'exists:users,id'],
            'event_id'      => ['nullable', 'exists:events,id'],
            'letter_number' => ['required', 'string', 'max:255', 'unique:documents,letter_number'],
            'title'         => ['required', 'string', 'max:255'],
            'type'          => ['nullable', 'in:incoming,outgoing'],
            'origin'        => ['nullable', 'string', 'max:255'],
            'destination'   => ['nullable', 'string', 'max:255'],
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
            'type'          => ['nullable', 'in:incoming,outgoing'],
            'origin'        => ['nullable', 'string', 'max:255'],
            'destination'   => ['nullable', 'string', 'max:255'],
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
            $url = Setting::where('key', 'bph_document_sync_url')->value('value');
            if (!$url) return response()->json(['message' => 'URL Sinkronisasi Dokumen BPH Pusat belum dikonfigurasi di Pengaturan.'], 500);
        }

        try {
            return response()->json($this->syncService->syncDocuments($eventId, $url, auth()->id() ?? 1));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyinkronisasi data.', 'error' => $e->getMessage()], 500);
        }
    }
}
