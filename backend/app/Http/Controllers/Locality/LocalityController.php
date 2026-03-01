<?php

namespace App\Http\Controllers\Locality;

use App\Http\Controllers\Controller;
use App\Http\Requests\Locality\LocalityRequest;
use App\Http\Resources\Locality\LocalityResource;
use App\Http\Repositories\Locality\LocalityRepository;
use App\Traits\UtilResponse;
use Illuminate\Http\Request;

class LocalityController extends Controller
{
    use UtilResponse;
    private $localityRepository;

    public function __construct(LocalityRepository $localityRepository)
    {
        $this->localityRepository = $localityRepository;
    }

    public function index()
    {
        return $this->successResponse(
            LocalityResource::collection($this->localityRepository->all()),
            'Localidades obtenidas correctamente'
        );
    }

    public function show($id)
    {
        $locality = $this->localityRepository->find($id);
        if ($locality) {
            return $this->successResponse(new LocalityResource($locality), 'Localidad encontrada');
        }
        return $this->errorResponse('No existe la localidad');
    }

    public function store(LocalityRequest $request)
    {
        $locality = $this->localityRepository->create($request->validated());
        if ($locality) {
            return $this->successResponse(new LocalityResource($locality), 'Localidad creada correctamente', 201);
        }
        return $this->errorResponse('Error al crear la localidad');
    }

    public function update(LocalityRequest $request, $id)
    {
        $locality = $this->localityRepository->update($id, $request->validated());
        if ($locality) {
            return $this->successResponse(new LocalityResource($locality), 'Localidad actualizada correctamente');
        }
        return $this->errorResponse('No se pudo actualizar la localidad o no existe');
    }

    public function destroy($id)
    {
        if ($this->localityRepository->delete($id)) {
            return $this->successResponse(null, 'Localidad eliminada correctamente');
        }
        return $this->errorResponse('No se pudo eliminar la localidad o no existe');
    }
}
