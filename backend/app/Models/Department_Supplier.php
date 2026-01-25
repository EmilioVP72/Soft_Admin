<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department_Supplier extends Model
{
    protected $table = 'department_suppliers';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'montos',
        'fk1_id_supplier',
        'fk2_id_department'
    ];

    /**
     * Relación Inverse Many to One con Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'fk1_id_supplier', 'id_supplier');
    }

    /**
     * Relación Inverse Many to One con Department
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'fk2_id_department', 'id_department');
    }
}
