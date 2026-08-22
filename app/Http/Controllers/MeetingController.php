<?php

namespace App\Http\Controllers;

use App\Http\Resources\MeetingResource;
use App\Models\Meeting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MeetingController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $meetings = Meeting::with(['attendances', 'event'])
            ->when($request->search, fn ($q, $search) =>
                $q->where('title', 'like', "%{$search}%")
            )
            ->where('event_id', $request->input('event_id'))
            ->latest('date')
            ->paginate(15);

        return MeetingResource::collection($meetings);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeEventAccess($request->user(), $request->event_id, ['Ketua', 'Sekretaris']);

        $validated = $request->validate([
            'event_id'    => ['nullable', 'exists:events,id'],
            'title'       => ['required', 'string', 'max:255'],
            'date'        => ['required', 'date'],
            'minutes_url' => ['nullable', 'string', 'max:255'],
        ]);

        $meeting = Meeting::create($validated);

        return response()->json(['message' => 'Success', 'data' => new MeetingResource($meeting)], 201);
    }

    public function update(Request $request, Meeting $meeting): JsonResponse
    {
        $this->authorizeEventAccess($request->user(), $meeting->event_id, ['Ketua', 'Sekretaris']);

        $validated = $request->validate([
            'event_id'    => ['nullable', 'exists:events,id'],
            'title'       => ['required', 'string', 'max:255'],
            'date'        => ['required', 'date'],
            'minutes_url' => ['nullable', 'string', 'max:255'],
        ]);

        $meeting->update($validated);

        return response()->json([
            'message' => 'Success',
            'data'    => new MeetingResource($meeting->load(['attendances', 'event'])),
        ]);
    }

    public function destroy(Request $request, Meeting $meeting): JsonResponse
    {
        $this->authorizeEventAccess($request->user(), $meeting->event_id, ['Ketua', 'Sekretaris']);

        $meeting->delete();

        return response()->json([
            'message' => 'Success',
        ]);
    }
}
