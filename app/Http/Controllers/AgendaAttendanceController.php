<?php

namespace App\Http\Controllers;

use App\Models\AgendaAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgendaAttendanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'agenda_id' => 'required|exists:agendas,id'
        ]);

        $attendances = AgendaAttendance::with('user')
            ->where('agenda_id', $request->agenda_id)
            ->get();

        return response()->json($attendances);
    }

    public function bulkSync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'agenda_id'               => 'required|exists:agendas,id',
            'attendances'             => 'required|array',
            'attendances.*.user_id'   => 'required|exists:users,id',
            'attendances.*.status'    => 'required|in:present,permit,sick,absent',
            'attendances.*.proof_url' => 'nullable|url',
        ]);

        $agendaId = $validated['agenda_id'];

        DB::transaction(function () use ($agendaId, $validated) {
            // Hapus absensi lama agar bisa ditimpa (Wipe & Reload)
            AgendaAttendance::where('agenda_id', $agendaId)->delete();

            // Masukkan absensi baru
            foreach ($validated['attendances'] as $att) {
                AgendaAttendance::create([
                    'agenda_id' => $agendaId,
                    'user_id'   => $att['user_id'],
                    'status'    => $att['status'],
                    'proof_url' => $att['proof_url'] ?? null,
                ]);
            }
        });

        return response()->json(['message' => 'Data absensi berhasil disimpan.']);
    }
}
