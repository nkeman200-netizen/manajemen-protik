<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $events = Event::with('committees.user')
            ->when($request->search, fn ($q, $search) =>
                $q->where('name', 'like', "%{$search}%")
            )
            ->latest()
            ->paginate(15);

        return response()->json([
            'message' => 'Success',
            'data' => $events,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'budget_approved'  => ['required', 'numeric', 'min:0'],
            'drive_folder_url' => ['nullable', 'string', 'max:255'],
            'start_date'       => ['required', 'date'],
            'end_date'         => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $event = Event::create($validated);

        return response()->json([
            'message' => 'Success',
            'data' => $event->load('committees.user'),
        ], 201);
    }

    public function update(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'budget_approved'  => ['required', 'numeric', 'min:0'],
            'drive_folder_url' => ['nullable', 'string', 'max:255'],
            'start_date'       => ['required', 'date'],
            'end_date'         => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $event->update($validated);

        return response()->json([
            'message' => 'Success',
            'data' => $event->load('committees.user'),
        ]);
    }

    public function destroy(Event $event): JsonResponse
    {
        $event->delete();

        return response()->json([
            'message' => 'Success',
        ]);
    }
}
