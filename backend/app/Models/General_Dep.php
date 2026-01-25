<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class General_Dep extends Model
{
    protected $table = 'general_deps';
    protected $primaryKey = 'id_general_dep';
    public $timestamps = true;

    protected $fillable = [
        'g_departament',
        'g_descripcion',
        'fkl_id_tienda'
    ];

    /**
     * Relación Inverse Many to One con Store
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'fkl_id_tienda', 'id_store');
    }

    /**
     * Relación One to Many con Department
     */
    public function departments()
    {
        return $this->hasMany(Department::class, 'fk1_id_general_dep', 'id_general_dep');
    }
}
