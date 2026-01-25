<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment_Type extends Model
{
    protected $table = 'payment_types';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'type',
        'fkl_id_store',
        'fk2_id_payment'
    ];

    /**
     * Relación Inverse Many to One con Store
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'fkl_id_store', 'id_store');
    }

    /**
     * Relación Inverse Many to One con Payments
     */
    public function payment()
    {
        return $this->belongsTo(Payments::class, 'fk2_id_payment', 'id_payment');
    }
}
