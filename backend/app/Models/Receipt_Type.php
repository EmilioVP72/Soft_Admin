<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt_Type extends Model
{
    protected $table = 'receipt_types';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'type',
        'fkl_id_store',
        'fk2_id_receipt'
    ];

    /**
     * Relación Inverse Many to One con Store
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'fkl_id_store', 'id_store');
    }

    /**
     * Relación Inverse Many to One con Receipts
     */
    public function receipt()
    {
        return $this->belongsTo(Receipts::class, 'fk2_id_receipt', 'id_receipt');
    }
}
