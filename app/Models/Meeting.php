<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model {
    use HasFactory;

    protected $fillable = ['event_id', 'title', 'date', 'minutes_url'];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function event(): BelongsTo {
        return $this->belongsTo(Event::class);
    }

    public function attendances(): HasMany {
        return $this->hasMany(MeetingAttendance::class);
    }
}
