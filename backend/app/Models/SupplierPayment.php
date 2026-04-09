<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPayment extends Model
{
    protected $table = 'supplier_payments';
    protected $primaryKey = 'id_supplier_payment';
    public $timestamps = true;

    protected $fillable = [
        'fk1_id_supplier',
        'fk2_id_department',
        'amount_paid',
        'payment_date'
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'fk1_id_supplier', 'id_supplier');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'fk2_id_department', 'id_department');
    }
}
