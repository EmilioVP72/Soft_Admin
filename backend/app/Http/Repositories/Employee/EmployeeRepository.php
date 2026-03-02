<?php

namespace App\Http\Repositories\Employee;

use App\Models\Employee;

class EmployeeRepository
{
    protected $employee;

    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    /**
     * Obtener todos los empleados con relaciones
     */
    public function all()
    {
        return $this->employee->with(['user', 'store'])->get();
    }

    /**
     * Obtener empleados paginados
     */
    public function paginated($perPage = 15)
    {
        return $this->employee->with(['user', 'store'])->paginate($perPage);
    }

    /**
     * Buscar empleado por ID
     */
    public function find($id)
    {
        return $this->employee->with(['user', 'store'])->find($id);
    }

    /**
     * Obtener empleados activos
     */
    public function getActive()
    {
        return $this->employee->active()->with(['user', 'store'])->get();
    }

    /**
     * Obtener empleados por tienda
     */
    public function getByStore($storeId)
    {
        return $this->employee->byStore($storeId)->with(['user', 'store'])->get();
    }

    /**
     * Obtener empleados por posición
     */
    public function getByPosition($position)
    {
        return $this->employee->byPosition($position)->with(['user', 'store'])->get();
    }

    /**
     * Crear nuevo empleado
     */
    public function create(array $data)
    {
        return $this->employee->create($data);
    }

    /**
     * Actualizar empleado
     */
    public function update($id, array $data)
    {
        $employee = $this->find($id);
        if ($employee) {
            $employee->update($data);
        }
        return $employee;
    }

    /**
     * Eliminar empleado (soft delete)
     */
    public function delete($id)
    {
        $employee = $this->find($id);
        return $employee?->delete();
    }

    /**
     * Buscar empleado por email
     */
    public function findByEmail($email)
    {
        return $this->employee->where('email', $email)->with(['user', 'store'])->first();
    }

    /**
     * Buscar empleado por document_number
     */
    public function findByDocument($documentNumber)
    {
        return $this->employee->where('document_number', $documentNumber)->with(['user', 'store'])->first();
    }

    /**
     * Búsqueda general por nombre, email o documento
     */
    public function search($query)
    {
        return $this->employee->where('full_name', 'like', "%$query%")
            ->orWhere('email', 'like', "%$query%")
            ->orWhere('document_number', 'like', "%$query%")
            ->with(['user', 'store'])
            ->get();
    }

    /**
     * Restaurar empleado eliminado
     */
    public function restore($id)
    {
        return $this->employee->withTrashed()->find($id)?->restore();
    }

    /**
     * Obtener empleados eliminados
     */
    public function getTrashed()
    {
        return $this->employee->onlyTrashed()->with(['user', 'store'])->get();
    }

    /**
     * Eliminar permanentemente
     */
    public function forceDelete($id)
    {
        $employee = $this->employeeRepository->employee
            ->withTrashed()
            ->find($id);
        return $employee?->forceDelete();
    }
}
