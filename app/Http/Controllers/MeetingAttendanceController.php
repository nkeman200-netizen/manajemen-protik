<?php
namespace App\Http\Controllers;

use App\Http\Resources\MeetingAttendanceResource;
use App\Models\MeetingAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MeetingAttendanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate(['meeting_id' => 'required|exists:meetings,id']);

        $attendances = MeetingAttendance::with(['user'])
            ->where('meeting_id', $request->meeting_id)
            ->get();

        return response()->json([
            'message' => 'Success',
            'data'    => MeetingAttendanceResource::collection($attendances),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'meeting_id' => ['required', 'exists:meetings,id'],
            'user_id'    => ['required', 'exists:users,id'],
            'status'     => ['required', 'in:present,permit,sick,absent'],
            'proof_url'  => ['nullable', 'string', 'max:255'],
        ]);

        $attendance = MeetingAttendance::updateOrCreate(
            ['meeting_id' => $validated['meeting_id'], 'user_id' => $validated['user_id']],
            ['status' => $validated['status'], 'proof_url' => $validated['proof_url']]
        );

        return response()->json([
            'message' => 'Success',
            'data'    => new MeetingAttendanceResource($attendance),
        ], 201);
    }

    public function bulkStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'meeting_id'              => ['required', 'exists:meetings,id'],
            'attendances'             => ['required', 'array'],
            'attendances.*.user_id'   => ['required', 'exists:users,id'],
            'attendances.*.status'    => ['required', 'in:present,permit,sick,absent'],
            'attendances.*.proof_url' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['attendances'] as $att) {
                MeetingAttendance::updateOrCreate(
                    [
                        'meeting_id' => $validated['meeting_id'],
                        'user_id'    => $att['user_id']
                    ],
                    [
                        'status'    => $att['status'],
                        'proof_url' => $att['proof_url'] ?? null
                    ]
                );
            }
        });

        return response()->json(['message' => 'Absensi massal berhasil disimpan.']);
    }
}
