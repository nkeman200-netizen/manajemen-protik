<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Models\Event;
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

        try {
            return response()->json($this->syncService->syncDocuments($eventId, $url, auth()->id() ?? 1));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyinkronisasi data.', 'error' => $e->getMessage()], 500);
        }
    }
}
