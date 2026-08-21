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
            ->whereIn('position', $allowedPositions)
            ->exists();
            
        if (!$isCommittee) abort(403, 'Anda tidak memiliki hak akses untuk Event ini.');
    }
}
