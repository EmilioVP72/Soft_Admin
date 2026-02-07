<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionDetail extends Model
{
    protected $table = 'transaction_details';
    protected $primaryKey = 'id_transaction_detail';
    public $timestamps = true;

    protected $fillable = [
        'fk1_id_transaction',
        'fk2_id_department',
        'quantity',
        'unit_price',
        'subtotal'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the transaction associated with this detail.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'fk1_id_transaction', 'id_transaction');
    }

    /**
     * Get the department associated with this detail.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'fk2_id_department', 'id_department');
    }
}
