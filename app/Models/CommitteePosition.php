<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommitteePosition extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_bph'];

    protected $casts = [
        'is_bph' => 'boolean',
    ];

    public function eventCommittees(): HasMany
    {
        return $this->hasMany(EventCommittee::class, 'position_id');
    }
}
