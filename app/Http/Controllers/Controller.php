<?php

namespace App\Http\Controllers;

use App\Models\EventCommittee;

abstract class Controller
{
    protected function authorizeEventAccess($user, $eventId, array $allowedPositions): void
    {
        if ($user->hasRole('admin')) return;
        if (!$eventId) abort(403, 'Anda tidak memiliki hak akses untuk Kas/Dokumen Umum.');
        
        $isCommittee = EventCommittee::where('event_id', $eventId)
            ->where('user_id', $user->id)
            ->whereHas('position', function ($query) use ($allowedPositions) {
                $query->whereIn('name', $allowedPositions)
                      ->orWhere('is_bph', true);
            })
            ->exists();
            
        if (!$isCommittee) abort(403, 'Anda tidak memiliki hak akses untuk Event ini.');
    }
}
