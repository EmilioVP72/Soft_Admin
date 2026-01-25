<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = 'stores';
    protected $primaryKey = 'id_store';
    public $timestamps = true;

    protected $fillable = [
        'store',
        'colony',
        'street',
        'exterior_number',
        'interior_number',
        'reference',
        'fk1_id_locality'
    ];

    public function locality()
    {
        return $this->belongsTo(Locality::class, 'fk1_id_locality', 'id_locality');
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_stores',
            'fk2_id_store',
            'fk1_id_user'
        )->withPivot('salary');
    }

    public function generalDeps()
    {
        return $this->hasMany(General_Dep::class, 'fkl_id_tienda', 'id_store');
    }

    public function paymentTypes()
    {
        return $this->hasMany(Payment_Type::class, 'fkl_id_store', 'id_store');
    }

    public function receiptTypes()
    {
        return $this->hasMany(Receipt_Type::class, 'fkl_id_store', 'id_store');
    }
}
