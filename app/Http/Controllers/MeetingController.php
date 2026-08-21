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
        $meetings = Meeting::with('attendances')
            ->when($request->search, fn ($q, $search) =>
                $q->where('title', 'like', "%{$search}%")
            )
            ->latest('date')
            ->paginate(15);

        return MeetingResource::collection($meetings);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'date'        => ['required', 'date'],
            'minutes_url' => ['nullable', 'string', 'max:255'],
        ]);

        $meeting = Meeting::create($validated);

        return response()->json(['message' => 'Success', 'data' => new MeetingResource($meeting)], 201);
    }

    public function update(Request $request, Meeting $meeting): JsonResponse
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'date'        => ['required', 'date'],
            'minutes_url' => ['nullable', 'string', 'max:255'],
        ]);

        $meeting->update($validated);

        return response()->json([
            'message' => 'Success',
            'data'    => new MeetingResource($meeting->load('attendances')),
        ]);
    }

    public function destroy(Meeting $meeting): JsonResponse
    {
        $meeting->delete();

        return response()->json([
            'message' => 'Success',
        ]);
    }
}
