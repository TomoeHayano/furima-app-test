<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'buyer_id',
        'seller_id',
        'buyer_rating',
        'seller_rating',
        'buyer_rated_at',
        'seller_rated_at',
        'buyer_completed_at',
        'seller_completed_at',
        'completed_at',
    ];

    /* ======================
     |  Relations
     |====================== */

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TransactionMessage::class);
    }

    /* ======================
     |  Helper
     |====================== */

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
