<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model {
    use HasFactory;

    protected $fillable = [
        'created_by', 'event_id', 'letter_number', 'title', 'drive_url',
    ];

    protected $casts = [
        'created_by' => 'integer',
        'event_id' => 'integer',
    ];

    public function creator(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function event(): BelongsTo {
        return $this->belongsTo(Event::class);
    }
}
