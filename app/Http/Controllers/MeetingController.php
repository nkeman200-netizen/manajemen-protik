<?php
namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $meetings = Meeting::with('attendances')
            ->when($request->search, fn ($q, $search) =>
                $q->where('title', 'like', "%{$search}%")
            )
            ->latest('date')
            ->paginate(15);

        return response()->json($meetings);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'date'        => ['required', 'date'],
            'minutes_url' => ['nullable', 'string', 'max:255'],
        ]);

        $meeting = Meeting::create($validated);

        return response()->json(['message' => 'Success', 'data' => $meeting], 201);
    }
}
