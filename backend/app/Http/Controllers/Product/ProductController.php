<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Product\ProductRepository;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Product\ProductResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    use UtilResponse;
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index(): JsonResponse
    {
        try {
            $products = $this->productRepository->getAll();
            return $this->successResponse(
                ProductResource::collection($products),
                'Productos obtenidos correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener productos: ' . $e->getMessage(), 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $product = $this->productRepository->find($id);
            if (!$product) {
                return $this->errorResponse('Producto no encontrado', 404);
            }
            return $this->successResponse(
                new ProductResource($product),
                'Producto obtenido correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener el producto: ' . $e->getMessage(), 500);
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productRepository->create($request->validated());
            return $this->successResponse(
                new ProductResource($product),
                'Producto creado correctamente',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al crear el producto: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdateProductRequest $request, $id): JsonResponse
    {
        try {
            $product = $this->productRepository->find($id);
            if (!$product) {
                return $this->errorResponse('Producto no encontrado', 404);
            }
            $product = $this->productRepository->update($product, $request->validated());
            return $this->successResponse(
                new ProductResource($product),
                'Producto actualizado correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al actualizar el producto: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $product = $this->productRepository->find($id);
            if (!$product) {
                return $this->errorResponse('Producto no encontrado', 404);
            }
            $this->productRepository->delete($product);
            return $this->successResponse(null, 'Producto eliminado correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse('Error al eliminar el producto: ' . $e->getMessage(), 500);
        }
    }
}
