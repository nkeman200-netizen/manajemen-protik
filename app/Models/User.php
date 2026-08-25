<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
    use HasFactory, HasRoles, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'status', 'division_id',
        'is_coordinator', 'nim', 'phone', 'prodi', 'angkatan', 'address'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password'       => 'hashed',
        'is_coordinator' => 'boolean',
    ];

    public function division(): BelongsTo {
        return $this->belongsTo(Division::class);
    }

    public function finances(): HasMany {
        return $this->hasMany(Finance::class);
    }
}