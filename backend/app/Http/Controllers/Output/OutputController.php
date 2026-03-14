<?php

namespace App\Http\Controllers\Output;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Output\OutputRepository;
use App\Http\Requests\Output\StoreOutputRequest;
use App\Http\Requests\Output\UpdateOutputRequest;
use App\Http\Resources\Output\OutputResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;

class OutputController extends Controller
{
    use UtilResponse;
    private $outputRepository;

    public function __construct(OutputRepository $outputRepository)
    {
        $this->outputRepository = $outputRepository;
    }

    public function index(): JsonResponse
    {
        try {
            $outputs = $this->outputRepository->getAll();
            return $this->successResponse(
                OutputResource::collection($outputs),
                'Salidas obtenidas correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener salidas: ' . $e->getMessage(), 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $output = $this->outputRepository->find($id);
            if (!$output) {
                return $this->errorResponse('Salida no encontrada', 404);
            }
            return $this->successResponse(
                new OutputResource($output),
                'Salida obtenida correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener la salida: ' . $e->getMessage(), 500);
        }
    }

    public function store(StoreOutputRequest $request): JsonResponse
    {
        try {
            $output = $this->outputRepository->create($request->validated());
            return $this->successResponse(
                new OutputResource($output),
                'Salida creada correctamente',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al crear la salida: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdateOutputRequest $request, $id): JsonResponse
    {
        try {
            $output = $this->outputRepository->find($id);
            if (!$output) {
                return $this->errorResponse('Salida no encontrada', 404);
            }
            $updatedOutput = $this->outputRepository->update($output, $request->validated());
            return $this->successResponse(
                new OutputResource($updatedOutput),
                'Salida actualizada correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al actualizar la salida: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $output = $this->outputRepository->find($id);
            if (!$output) {
                return $this->errorResponse('Salida no encontrada', 404);
            }
            $this->outputRepository->delete($output);
            return $this->successResponse(null, 'Salida eliminada correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse('Error al eliminar la salida: ' . $e->getMessage(), 500);
        }
    }
}
