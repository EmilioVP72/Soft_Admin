<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\StoreRequest;
use App\Http\Resources\Store\StoreResource;
use App\Http\Repositories\Store\StoreRepository;
use App\Traits\UtilResponse;

class StoreController extends Controller
{
    private $utilResponse;
    private $storeRepository;

    public function __construct(UtilResponse $utilResponse, StoreRepository $storeRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->storeRepository = $storeRepository;
    }

    public function index()
    {
        return $this->utilResponse->succesResponse(StoreResource::collection($this->storeRepository->all()), 'Tiendas obtenidas correctamente');
    }

    public function show($id)
    {
        $store = $this->storeRepository->find($id);
        if ($store) {
            return $this->utilResponse->succesResponse(new StoreResource($store), 'Tienda encontrada');
        }
        return $this->utilResponse->errorResponse('No existe la tienda');
    }

    public function store(StoreRequest $request)
    {
        $store = $this->storeRepository->create($request->validated());
        if ($store) {
            return $this->utilResponse->succesResponse(new StoreResource($store), 'Tienda creada correctamente', 201);
        }
        return $this->utilResponse->errorResponse('Error al crear la tienda');
    }

    public function update(StoreRequest $request, $id)
    {
        $store = $this->storeRepository->update($id, $request->validated());
        return $this->utilResponse->succesResponse(new StoreResource($store), 'Tienda actualizada correctamente');
    }

    public function destroy($id)
    {
        if ($this->storeRepository->delete($id)) {
            return $this->utilResponse->succesResponse(null, 'Tienda eliminada correctamente');
        }
        return $this->utilResponse->errorResponse('No se pudo eliminar la tienda o no existe');
    }
}
