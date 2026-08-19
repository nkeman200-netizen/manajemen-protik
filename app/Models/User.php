<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable {
    use HasFactory;
    protected $fillable = ['name', 'email', 'password', 'status', 'division_id'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'password' => 'hashed',
    ];

    public function division(): BelongsTo {
        return $this->belongsTo(Division::class);
    }

    public function finances(): HasMany {
        return $this->hasMany(Finance::class);
    }
}