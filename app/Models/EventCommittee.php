<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventCommittee extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'user_id', 'position_id'];

    public function position(): BelongsTo
    {
        return $this->belongsTo(CommitteePosition::class, 'position_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
