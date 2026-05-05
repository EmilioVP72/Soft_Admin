<?php

namespace App\Http\Controllers\ProductInventory;

use App\Http\Controllers\Controller;
use App\Http\Repositories\ProductInventory\ProductInventoryRepository;
use App\Http\Requests\ProductInventory\StoreProductInventoryRequest;
use App\Http\Requests\ProductInventory\VerifyProductInventoryRequest;
use App\Http\Resources\ProductInventory\ProductInventoryResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;

class ProductInventoryController extends Controller
{
    use UtilResponse;
    private $inventoryRepository;

    public function __construct(ProductInventoryRepository $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    public function index(): JsonResponse
    {
        try {
            $inventories = $this->inventoryRepository->getAll();
            return $this->successResponse(
                ProductInventoryResource::collection($inventories),
                'Inventarios obtenidos correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener inventarios: ' . $e->getMessage(), 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $inventory = $this->inventoryRepository->find($id);
            if (!$inventory) {
                return $this->errorResponse('Inventario no encontrado', 404);
            }
            return $this->successResponse(
                new ProductInventoryResource($inventory),
                'Inventario obtenido correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener el inventario: ' . $e->getMessage(), 500);
        }
    }

    public function store(StoreProductInventoryRequest $request): JsonResponse
    {
        try {
            $inventory = $this->inventoryRepository->create($request->validated());
            return $this->successResponse(
                new ProductInventoryResource($inventory),
                'Inventario creado correctamente',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al crear el inventario: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $inventory = $this->inventoryRepository->find($id);
            if (!$inventory) {
                return $this->errorResponse('Inventario no encontrado', 404);
            }
            $this->inventoryRepository->delete($inventory);
            return $this->successResponse(null, 'Inventario eliminado correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse('Error al eliminar el inventario: ' . $e->getMessage(), 500);
        }
    }

    public function getByProduct($productId): JsonResponse
    {
        try {
            $inventories = $this->inventoryRepository->getByProduct($productId);
            return $this->successResponse(
                ProductInventoryResource::collection($inventories),
                'Inventarios del producto obtenidos correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener los inventarios del producto: ' . $e->getMessage(), 500);
        }
    }

    public function verify(VerifyProductInventoryRequest $request, $id): JsonResponse
    {
        try {
            $inventory = $this->inventoryRepository->find($id);
            if (!$inventory) {
                return $this->errorResponse('Inventario no encontrado', 404);
            }
            
            $inventory->verify($request->physical_quantity, $request->notes);
            
            return $this->successResponse(
                new ProductInventoryResource($inventory),
                'Inventario verificado correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al verificar el inventario: ' . $e->getMessage(), 500);
        }
    }
}
