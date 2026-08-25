<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendaTarget extends Model {
    use HasFactory;

    protected $fillable = ['agenda_id', 'target_type', 'target_value'];

    public function agenda(): BelongsTo {
        return $this->belongsTo(Agenda::class);
    }
}
