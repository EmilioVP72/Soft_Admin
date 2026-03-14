<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id_transaction';
    public $timestamps = true;

    protected $fillable = [
        'fk1_id_store',
        'fk2_id_user',
        'fk3_id_payment',
        'total_amount',
        'transaction_type',
        'notes',
        'transaction_date'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    /**
     * Get the store associated with the transaction.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'fk1_id_store', 'id_store');
    }

    /**
     * Get the user associated with the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fk2_id_user', 'id_user');
    }

    /**
     * Get the transaction details.
     */
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class, 'fk1_id_transaction', 'id_transaction');
    }

    /**
     * Get the payment type associated with the transaction.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payments::class, 'fk3_id_payment', 'id_payment');
    }
}
