<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $table = 'employees';
    protected $primaryKey = 'id_employee';
    public $timestamps = true;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'document_type',
        'document_number',
        'position',
        'salary',
        'status',
        'hire_date',
        'end_date',
        'fk_id_user',
        'fk_id_store',
        'notes',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'end_date' => 'date',
        'salary' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [];

    /**
     * Relación: Un empleado pertenece a un usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'fk_id_user', 'id_user');
    }

    /**
     * Relación: Un empleado pertenece a una tienda
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'fk_id_store', 'id_store');
    }

    /**
     * Scope para obtener solo empleados activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope para obtener empleados por tienda
     */
    public function scopeByStore($query, $storeId)
    {
        return $query->where('fk_id_store', $storeId);
    }

    /**
     * Scope para obtener empleados por posición
     */
    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Obtener datos del empleado con usuario y tienda (relaciones)
     */
    public function withRelations()
    {
        return $this->load(['user', 'store']);
    }

    /**
     * Verificar si el empleado tiene usuario asignado
     */
    public function hasUser()
    {
        return $this->fk_id_user !== null;
    }

    /**
     * Obtener estado del empleado en texto legible
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'Active' => 'Activo',
            'Inactive' => 'Inactivo',
            'On Leave' => 'En Licencia',
        ];
        return $statuses[$this->status] ?? $this->status;
    }
}
