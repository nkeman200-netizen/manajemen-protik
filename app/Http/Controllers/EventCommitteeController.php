<?php

namespace App\Http\Controllers;

use App\Models\CommitteePosition;
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

        $committees = EventCommittee::with(['user', 'position'])
            ->where('event_id', $request->event_id)
            ->get();

        return response()->json([
            'message' => 'Success',
            'data'    => $committees,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_id'    => ['required', 'exists:events,id'],
            'user_id'     => ['required', 'exists:users,id'],
            'position_id' => ['nullable', 'exists:committee_positions,id'],
            'position'    => ['nullable', 'string'],
        ]);

        if (empty($validated['position_id']) && !empty($validated['position'])) {
            $pos = CommitteePosition::firstOrCreate(
                ['name' => $validated['position']],
                ['is_bph' => false]
            );
            $validated['position_id'] = $pos->id;
        }

        unset($validated['position']);

        $committee = EventCommittee::create($validated);

        return response()->json([
            'message' => 'Success',
            'data'    => $committee->load(['user', 'position']),
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
