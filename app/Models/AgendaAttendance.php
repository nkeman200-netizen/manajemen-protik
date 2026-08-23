<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendaAttendance extends Model {
    use HasFactory;

    protected $fillable = ['agenda_id', 'user_id', 'status', 'proof_url'];
    
    public function agenda(): BelongsTo { return $this->belongsTo(Agenda::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
