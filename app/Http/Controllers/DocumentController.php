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
            ->when($request->event_id, fn ($q, $eventId) =>
                $q->where('event_id', $eventId)
            )
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
}
