<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agenda extends Model {
    use HasFactory;

    protected $fillable = ['event_id', 'title', 'start_date', 'end_date', 'location', 'pic', 'status', 'minutes_url'];
    protected $casts = ['start_date' => 'datetime', 'end_date' => 'datetime'];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function attendances(): HasMany { return $this->hasMany(AgendaAttendance::class); }
}
