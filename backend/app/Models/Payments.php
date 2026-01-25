<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id_payment';
    public $timestamps = true;

    protected $fillable = [
        'payment',
        'description'
    ];

    /**
     * Relación One to Many con Payment_Type
     */
    public function paymentTypes()
    {
        return $this->hasMany(Payment_Type::class, 'fk2_id_payment', 'id_payment');
    }
}
