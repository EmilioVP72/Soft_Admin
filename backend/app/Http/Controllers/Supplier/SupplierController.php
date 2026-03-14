<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Supplier\SupplierRepository;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Http\Requests\Supplier\StoreSupplierPaymentRequest;
use App\Http\Resources\Supplier\SupplierResource;
use App\Http\Resources\Supplier\SupplierPaymentResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    use UtilResponse;
    private $supplierRepository;

    public function __construct(SupplierRepository $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    public function index(): JsonResponse 
    {
        try {
            $suppliers = $this->supplierRepository->getAll();
            return $this->successResponse(
                SupplierResource::collection($suppliers), 
                'Proveedores obtenidos correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener proveedores: ' . $e->getMessage(), 500);
        }
    }

    public function show($id): JsonResponse 
    {
        try {
            $supplier = $this->supplierRepository->find($id);
            if (!$supplier) {
                return $this->errorResponse('Proveedor no encontrado', 404);
            }
            return $this->successResponse(
                new SupplierResource($supplier), 
                'Proveedor obtenido correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener el proveedor: ' . $e->getMessage(), 500);
        }
    }

    public function store(StoreSupplierRequest $request): JsonResponse 
    {
        try {
            $supplier = $this->supplierRepository->create($request->validated());
            return $this->successResponse(
                new SupplierResource($supplier), 
                'Proveedor creado correctamente', 
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al crear el proveedor: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdateSupplierRequest $request, $id): JsonResponse 
    {
        try {
            $supplier = $this->supplierRepository->find($id);
            if (!$supplier) {
                return $this->errorResponse('Proveedor no encontrado', 404);
            }
            $supplier = $this->supplierRepository->update($supplier, $request->validated());
            return $this->successResponse(
                new SupplierResource($supplier), 
                'Proveedor actualizado correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al actualizar el proveedor: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id): JsonResponse 
    {
        try {
            $supplier = $this->supplierRepository->find($id);
            if (!$supplier) {
                return $this->errorResponse('Proveedor no encontrado', 404);
            }
            $this->supplierRepository->delete($supplier);
            return $this->successResponse(null, 'Proveedor eliminado correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse('Error al eliminar el proveedor: ' . $e->getMessage(), 500);
        }
    }

    public function storePayment(StoreSupplierPaymentRequest $request, $id): JsonResponse 
    {
        try {
            $supplier = $this->supplierRepository->find($id);
            if (!$supplier) {
                return $this->errorResponse('Proveedor no encontrado', 404);
            }
            
            $data = $request->validated();
            $data['fk1_id_supplier'] = $id;
            
            $payment = $this->supplierRepository->createPayment($data);
            return $this->successResponse(
                new SupplierPaymentResource($payment), 
                'Pago a proveedor registrado correctamente', 
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al registrar el pago: ' . $e->getMessage(), 500);
        }
    }

    public function getAllPayments(): JsonResponse 
    {
        try {
            $payments = $this->supplierRepository->getAllPayments();
            return $this->successResponse(
                SupplierPaymentResource::collection($payments), 
                'Pagos obtenidos correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener los pagos: ' . $e->getMessage(), 500);
        }
    }

    public function getSupplierPayments($id): JsonResponse 
    {
        try {
            $supplier = $this->supplierRepository->find($id);
            if (!$supplier) {
                return $this->errorResponse('Proveedor no encontrado', 404);
            }
            $payments = $this->supplierRepository->getPaymentsBySupplier($id);
            return $this->successResponse(
                SupplierPaymentResource::collection($payments), 
                'Pagos del proveedor obtenidos correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener pagos: ' . $e->getMessage(), 500);
        }
    }
}
