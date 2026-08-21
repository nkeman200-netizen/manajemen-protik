<?php

namespace App\Http\Controllers;

use App\Models\EventCommittee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventCommitteeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'event_id' => ['required', 'exists:events,id'],
        ]);

        $committees = EventCommittee::with('user')
            ->where('event_id', $request->event_id)
            ->get();

        return response()->json([
            'message' => 'Success',
            'data' => $committees,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'user_id' => ['required', 'exists:users,id'],
            'position' => ['required', 'string'],
        ]);

        $committee = EventCommittee::create($validated);

        return response()->json([
            'message' => 'Success',
            'data' => $committee->load('user'),
        ], 201);
    }

    public function destroy(EventCommittee $eventCommittee): JsonResponse
    {
        $eventCommittee->delete();

        return response()->json([
            'message' => 'Success',
        ]);
    }
}
