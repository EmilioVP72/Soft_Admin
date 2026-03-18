<?php

namespace App\Http\Controllers\GeneralDepartment;

use App\Http\Controllers\Controller;
use App\Http\Repositories\GeneralDepartment\GeneralDepartmentRepository;
use App\Http\Requests\GeneralDepartment\StoreGeneralDepartmentRequest;
use App\Http\Requests\GeneralDepartment\UpdateGeneralDepartmentRequest;
use App\Http\Resources\GeneralDepartment\GeneralDepartmentResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;

class GeneralDepartmentController extends Controller
{
    use UtilResponse;
    private $generalDepartmentRepository;

    public function __construct(GeneralDepartmentRepository $generalDepartmentRepository)
    {
        $this->generalDepartmentRepository = $generalDepartmentRepository;
    }

    public function index(): JsonResponse 
    {
        try {
            $generalDeps = $this->generalDepartmentRepository->getAll();
            return $this->successResponse(
                GeneralDepartmentResource::collection($generalDeps), 
                'Departamentos generales obtenidos correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener departamentos generales: ' . $e->getMessage(), 500);
        }
    }

    public function show($id): JsonResponse 
    {
        try {
            $generalDep = $this->generalDepartmentRepository->find($id);
            if (!$generalDep) {
                return $this->errorResponse('Departamento general no encontrado', 404);
            }
            return $this->successResponse(
                new GeneralDepartmentResource($generalDep), 
                'Departamento general obtenido correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener el departamento general: ' . $e->getMessage(), 500);
        }
    }

    public function store(StoreGeneralDepartmentRequest $request): JsonResponse 
    {
        try {
            $generalDep = $this->generalDepartmentRepository->create($request->validated());
            $generalDep->load('store');
            return $this->successResponse(
                new GeneralDepartmentResource($generalDep), 
                'Departamento general creado correctamente', 
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al crear el departamento general: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdateGeneralDepartmentRequest $request, $id): JsonResponse 
    {
        try {
            $generalDep = $this->generalDepartmentRepository->find($id);
            if (!$generalDep) {
                return $this->errorResponse('Departamento general no encontrado', 404);
            }
            $generalDep = $this->generalDepartmentRepository->update($generalDep, $request->validated());
            $generalDep->load('store');
            return $this->successResponse(
                new GeneralDepartmentResource($generalDep), 
                'Departamento general actualizado correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al actualizar el departamento general: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id): JsonResponse 
    {
        try {
            $generalDep = $this->generalDepartmentRepository->find($id);
            if (!$generalDep) {
                return $this->errorResponse('Departamento general no encontrado', 404);
            }
            $this->generalDepartmentRepository->delete($generalDep);
            return $this->successResponse(null, 'Departamento general eliminado correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse('Error al eliminar el departamento general: ' . $e->getMessage(), 500);
        }
    }
}
