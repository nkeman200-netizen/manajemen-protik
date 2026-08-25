<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'budget_approved', 
        'drive_folder_url', 'start_date', 'end_date',
        'document_sync_url', 'finance_sync_url',
    ];

    // Konversi presisi data otomatis
    protected $casts = [
        'budget_approved' => 'decimal:2',
        'start_date'      => 'date',
        'end_date'        => 'date',
    ];

    public function finances(): HasMany {
        return $this->hasMany(Finance::class);
    }

    public function committees(): HasMany {
        return $this->hasMany(EventCommittee::class);
    }
}