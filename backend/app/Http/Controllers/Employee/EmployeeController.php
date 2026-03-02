<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeRequest;
use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Repositories\Employee\EmployeeRepository;
use App\Traits\UtilResponse;

class EmployeeController extends Controller
{
    use UtilResponse;
    private $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function index()
    {
        $employees = $this->employeeRepository->all();
        return $this->successResponse(
            EmployeeResource::collection($employees),
            'Empleados obtenidos correctamente'
        );
    }

    public function getActive()
    {
        $employees = $this->employeeRepository->getActive();
        return $this->successResponse(
            EmployeeResource::collection($employees),
            'Empleados activos obtenidos correctamente'
        );
    }

    public function getByStore($storeId)
    {
        $employees = $this->employeeRepository->getByStore($storeId);
        return $this->successResponse(
            EmployeeResource::collection($employees),
            'Empleados de la tienda obtenidos correctamente'
        );
    }

    public function getByPosition($position)
    {
        $employees = $this->employeeRepository->getByPosition($position);
        return $this->successResponse(
            EmployeeResource::collection($employees),
            'Empleados por posición obtenidos correctamente'
        );
    }

    public function search()
    {
        $query = request()->input('q');
        
        if (!$query || strlen($query) < 3) {
            return $this->errorResponse(
                'El término de búsqueda debe tener al menos 3 caracteres',
                400
            );
        }

        $employees = $this->employeeRepository->search($query);
        return $this->successResponse(
            EmployeeResource::collection($employees),
            'Búsqueda completada correctamente'
        );
    }

    public function show($id)
    {
        $employee = $this->employeeRepository->find($id);
        
        if (!$employee) {
            return $this->errorResponse(
                'El empleado no existe',
                404
            );
        }

        return $this->successResponse(
            new EmployeeResource($employee),
            'Empleado encontrado correctamente'
        );
    }

    public function store(EmployeeRequest $request)
    {
        $employee = $this->employeeRepository->create($request->validated());
        
        if (!$employee) {
            return $this->errorResponse(
                'Error al crear el empleado',
                500
            );
        }

        return $this->successResponse(
            new EmployeeResource($employee),
            'Empleado creado correctamente',
            201
        );
    }

    public function update(EmployeeRequest $request, $id)
    {
        $employee = $this->employeeRepository->find($id);
        
        if (!$employee) {
            return $this->errorResponse(
                'El empleado no existe',
                404
            );
        }

        $employee = $this->employeeRepository->update($id, $request->validated());
        
        return $this->successResponse(
            new EmployeeResource($employee),
            'Empleado actualizado correctamente'
        );
    }

    public function destroy($id)
    {
        $employee = $this->employeeRepository->find($id);
        
        if (!$employee) {
            return $this->errorResponse(
                'El empleado no existe',
                404
            );
        }

        if ($this->employeeRepository->delete($id)) {
            return $this->successResponse(
                null,
                'Empleado eliminado correctamente'
            );
        }

        return $this->errorResponse(
            'Error al eliminar el empleado',
            500
        );
    }

    public function restore($id)
    {
        $employee = $this->employeeRepository->employee
            ->withTrashed()
            ->find($id);
        
        if (!$employee) {
            return $this->errorResponse(
                'El empleado no existe',
                404
            );
        }

        if ($this->employeeRepository->restore($id)) {
            $restored = $this->employeeRepository->find($id);
            return $this->successResponse(
                new EmployeeResource($restored),
                'Empleado restaurado correctamente'
            );
        }

        return $this->errorResponse(
            'Error al restaurar el empleado',
            500
        );
    }

    public function getTrashed()
    {
        $employees = $this->employeeRepository->getTrashed();
        return $this->successResponse(
            EmployeeResource::collection($employees),
            'Empleados eliminados obtenidos correctamente'
        );
    }

    public function forceDelete($id)
    {
        $employee = $this->employeeRepository
            ->find($id)
            ->withTrashed()
            ->find($id);
    
        
        if (!$employee) {
            return $this->errorResponse(
                'El empleado no existe',
                404
            );
        }

        if ($this->employeeRepository->forceDelete($id)) {
            return $this->successResponse(
                null,
                'Empleado eliminado permanentemente'
            );
        }

        return $this->errorResponse(
            'Error al eliminar permanentemente el empleado',
            500
        );
    }
}
