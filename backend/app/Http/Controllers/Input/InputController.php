<?php

namespace App\Http\Controllers\Input;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Input\InputRepository;
use App\Http\Requests\Input\StoreInputRequest;
use App\Http\Requests\Input\UpdateInputRequest;
use App\Http\Resources\Input\InputResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;

class InputController extends Controller
{
    use UtilResponse;
    private $inputRepository;

    public function __construct(InputRepository $inputRepository)
    {
        $this->inputRepository = $inputRepository;
    }

    public function index(): JsonResponse
    {
        try {
            $inputs = $this->inputRepository->getAll();
            return $this->successResponse(
                InputResource::collection($inputs),
                'Entradas obtenidas correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener entradas: ' . $e->getMessage(), 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $input = $this->inputRepository->find($id);
            if (!$input) {
                return $this->errorResponse('Entrada no encontrada', 404);
            }
            return $this->successResponse(
                new InputResource($input),
                'Entrada obtenida correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener la entrada: ' . $e->getMessage(), 500);
        }
    }

    public function store(StoreInputRequest $request): JsonResponse
    {
        try {
            $input = $this->inputRepository->create($request->validated());
            return $this->successResponse(
                new InputResource($input),
                'Entrada creada correctamente',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al crear la entrada: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdateInputRequest $request, $id): JsonResponse
    {
        try {
            $input = $this->inputRepository->find($id);
            if (!$input) {
                return $this->errorResponse('Entrada no encontrada', 404);
            }
            $updatedInput = $this->inputRepository->update($input, $request->validated());
            return $this->successResponse(
                new InputResource($updatedInput),
                'Entrada actualizada correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al actualizar la entrada: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $input = $this->inputRepository->find($id);
            if (!$input) {
                return $this->errorResponse('Entrada no encontrada', 404);
            }
            $this->inputRepository->delete($input);
            return $this->successResponse(null, 'Entrada eliminada correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse('Error al eliminar la entrada: ' . $e->getMessage(), 500);
        }
    }
}
