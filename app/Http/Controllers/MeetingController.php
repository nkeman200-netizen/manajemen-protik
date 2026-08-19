<?php
namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index(): JsonResponse
    {
        $meetings = Meeting::with('attendances')->latest('date')->get();

        return response()->json(['message' => 'Success', 'data' => $meetings]);
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
