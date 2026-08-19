<?php
namespace App\Http\Controllers;

use App\Models\MeetingAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeetingAttendanceController extends Controller
{
    public function index(): JsonResponse
    {
        $attendances = MeetingAttendance::with(['meeting', 'user'])->latest()->get();

        return response()->json(['message' => 'Success', 'data' => $attendances]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'meeting_id' => ['required', 'exists:meetings,id'],
            'user_id'    => ['required', 'exists:users,id'],
            'status'     => ['required', 'in:present,permit,sick,absent'],
            'proof_url'  => ['nullable', 'string', 'max:255'],
        ]);

        $attendance = MeetingAttendance::create($validated);

        return response()->json(['message' => 'Success', 'data' => $attendance], 201);
    }
}
