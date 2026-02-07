<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'id_department';
    public $timestamps = true;

    protected $fillable = [
        'department',
        'description',
        'fk1_id_general_dep'
    ];

    /**
     * Relación Inverse Many to One con General_Dep
     */
    public function generalDep()
    {
        return $this->belongsTo(General_Dep::class, 'fk1_id_general_dep', 'id_general_dep');
    }

    /**
     * Relación Many to Many con Supplier a través de department_suppliers
     */
    public function suppliers()
    {
        return $this->belongsToMany(
            Supplier::class,
            'department_suppliers',
            'fk2_id_department',
            'fk1_id_supplier'
        )->withPivot('montos');
    }

    /**
     * Relación One to Many con TransactionDetail
     */
    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'fk2_id_department', 'id_department');
    }
}
