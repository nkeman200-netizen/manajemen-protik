<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Finance extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id', 'event_id', 'type', 'category', 'funding_source', 
        'title', 'description', 'qty', 'unit', 'unit_price', 'amount', 
        'pic', 'payment_method', 'notes', 'receipt_url', 'date'
    ];

    protected $casts = [
        'qty'        => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount'     => 'decimal:2',
        'date'       => 'date',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo {
        return $this->belongsTo(Event::class);
    }
}