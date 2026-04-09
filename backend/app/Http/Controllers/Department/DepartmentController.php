<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Department\DepartmentRepository;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Http\Resources\Department\DepartmentResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    use UtilResponse;
    private $departmentRepository;

    public function __construct(DepartmentRepository $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    public function index(): JsonResponse 
    {
        try {
            $departments = $this->departmentRepository->getAll();
            return $this->successResponse(
                DepartmentResource::collection($departments), 
                'Departamentos obtenidos correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener departamentos: ' . $e->getMessage(), 500);
        }
    }

    public function getByStore($storeId): JsonResponse 
    {
        try {
            $departments = $this->departmentRepository->getByStore($storeId);
            return $this->successResponse(
                DepartmentResource::collection($departments), 
                'Departamentos obtenidos correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener departamentos: ' . $e->getMessage(), 500);
        }
    }

    public function show($id): JsonResponse 
    {
        try {
            $department = $this->departmentRepository->find($id);
            if (!$department) {
                return $this->errorResponse('Departamento no encontrado', 404);
            }
            return $this->successResponse(
                new DepartmentResource($department), 
                'Departamento obtenido correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener el departamento: ' . $e->getMessage(), 500);
        }
    }

    public function store(StoreDepartmentRequest $request): JsonResponse 
    {
        try {
            $department = $this->departmentRepository->create($request->validated());
            $department->load('generalDep');
            return $this->successResponse(
                new DepartmentResource($department), 
                'Departamento creado correctamente', 
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al crear el departamento: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdateDepartmentRequest $request, $id): JsonResponse 
    {
        try {
            $department = $this->departmentRepository->find($id);
            if (!$department) {
                return $this->errorResponse('Departamento no encontrado', 404);
            }
            $department = $this->departmentRepository->update($department, $request->validated());
            $department->load('generalDep');
            return $this->successResponse(
                new DepartmentResource($department), 
                'Departamento actualizado correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al actualizar el departamento: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id): JsonResponse 
    {
        try {
            $department = $this->departmentRepository->find($id);
            if (!$department) {
                return $this->errorResponse('Departamento no encontrado', 404);
            }
            $this->departmentRepository->delete($department);
            return $this->successResponse(null, 'Departamento eliminado correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse('Error al eliminar el departamento: ' . $e->getMessage(), 500);
        }
    }
}
