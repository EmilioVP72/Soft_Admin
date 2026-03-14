<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';
    protected $primaryKey = 'id_supplier';
    public $timestamps = true;

    protected $fillable = [
        'supplier'
    ];

    public function departments()
    {
        return $this->belongsToMany(
            Department::class,
            'department_suppliers',
            'fk1_id_supplier',
            'fk2_id_department'
        )->withPivot('montos');
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class, 'fk1_id_supplier', 'id_supplier');
    }
}
