<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Models\EventCommittee;

class CheckEventBPH
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasRole('admin')) {
            return $next($request);
        }

        $eventId = $request->route('event') ?? $request->input('event_id');
        $eventId = is_object($eventId) ? $eventId->id : $eventId;

        if (!$eventId) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Konteks Event ID tidak ditemukan.'
            ], 403);
        }

        // Kueri teroptimasi berkat Indexing dan Foreign Key (Tanpa operasi LIKE)
        $isBph = EventCommittee::where('event_id', $eventId)
            ->where('user_id', $user->id)
            ->whereHas('position', function($query) {
                $query->where('is_bph', true); // Mengecek flag boolean (Sangat Cepat)
            })
            ->exists();

        if ($isBph) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Akses ditolak. Anda tidak memiliki wewenang BPH pada Event ini.'
        ], 403);
    }
}
