<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingAttendance extends Model {
    use HasFactory;

    protected $fillable = ['meeting_id', 'user_id', 'status', 'proof_url'];

    protected $casts = [
        'status' => 'string',
    ];

    public function meeting(): BelongsTo {
        return $this->belongsTo(Meeting::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
