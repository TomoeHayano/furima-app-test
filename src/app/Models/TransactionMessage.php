<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionMessage extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_id',
        'sender_id',
        'body',
        'image_path',
        'read_at',
    ];

    /* ======================
     |  Relations
     |====================== */

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /* ======================
     |  Helper
     |====================== */

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
