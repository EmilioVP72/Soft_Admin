<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\StoreRequest;
use App\Http\Resources\Store\StoreResource;
use App\Http\Repositories\Store\StoreRepository;
use App\Traits\UtilResponse;

class StoreController extends Controller
{
    use UtilResponse;
    private $storeRepository;

    public function __construct(StoreRepository $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    public function index()
    {
        return $this->successResponse(StoreResource::collection($this->storeRepository->all()), 'Tiendas obtenidas correctamente');
    }

    public function show($id)
    {
        $store = $this->storeRepository->find($id);
        if ($store) {
            return $this->successResponse(new StoreResource($store), 'Tienda encontrada');
        }
        return $this->errorResponse('No existe la tienda');
    }

    public function store(StoreRequest $request)
    {
        $store = $this->storeRepository->create($request->validated());
        if ($store) {
            return $this->successResponse(new StoreResource($store), 'Tienda creada correctamente', 201);
        }
        return $this->errorResponse('Error al crear la tienda');
    }

    public function update(StoreRequest $request, $id)
    {
        $store = $this->storeRepository->update($id, $request->validated());
        return $this->successResponse(new StoreResource($store), 'Tienda actualizada correctamente');
    }

    public function destroy($id)
    {
        if ($this->storeRepository->delete($id)) {
            return $this->successResponse(null, 'Tienda eliminada correctamente');
        }
        return $this->errorResponse('No se pudo eliminar la tienda o no existe');
    }
}
