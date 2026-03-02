<?php

namespace App\Http\Controllers\Sale;

use App\Models\Store;
use App\Models\Department;
use App\Http\Controllers\Controller;
use App\Http\Repositories\Sale\SalesRepository;
use App\Http\Requests\Sale\SalesFilterRequest;
use App\Http\Resources\Sale\DepartmentSalesResource;
use App\Http\Resources\Sale\GeneralDepartmentSalesResource;
use App\Http\Resources\Sale\TransactionResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;

class SalesController extends Controller
{
    use UtilResponse;
    private $salesRepository;

    public function __construct(SalesRepository $salesRepository)
    {
        $this->salesRepository = $salesRepository;
    }

    public function getSalesByDepartmentGeneral(): JsonResponse
    {
        try {
            $sales = $this->salesRepository->getSalesByDepartmentGeneral();
            
            if ($sales->isEmpty()) {
                return $this->errorResponse(
                    'No hay datos de ventas disponibles',
                    404
                );
            }

            return $this->successResponse(
                DepartmentSalesResource::collection($sales),
                'Ventas por departamento obtenidas correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error al obtener las ventas por departamento: ' . $e->getMessage(),
                500
            );
        }
    }

    public function getSalesByDepartmentByStore($storeId): JsonResponse
    {
        try {
            $store = Store::find($storeId);
            if (!$store) {
                return $this->errorResponse(
                    'La tienda no existe',
                    404
                );
            }

            $sales = $this->salesRepository->getSalesByDepartmentByStore($storeId);
            
            if ($sales->isEmpty()) {
                return $this->successResponse(
                    [],
                    'No hay ventas registradas para esta tienda'
                );
            }

            return $this->successResponse(
                DepartmentSalesResource::collection($sales),
                'Ventas por departamento de la tienda obtenidas correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error al obtener las ventas por departamento: ' . $e->getMessage(),
                500
            );
        }
    }

    public function getSalesByGeneralDepartment(): JsonResponse
    {
        try {
            $sales = $this->salesRepository->getSalesByGeneralDepartment();
            
            if ($sales->isEmpty()) {
                return $this->errorResponse(
                    'No hay datos de ventas disponibles',
                    404
                );
            }

            return $this->successResponse(
                GeneralDepartmentSalesResource::collection($sales),
                'Ventas por departamento general obtenidas correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error al obtener las ventas: ' . $e->getMessage(),
                500
            );
        }
    }

    public function getSalesByDepartmentStoreWithDates($storeId, SalesFilterRequest $request): JsonResponse
    {
        try {
            $store = Store::find($storeId);
            if (!$store) {
                return $this->errorResponse(
                    'La tienda no existe',
                    404
                );
            }

            $sales = $this->salesRepository->getSalesByDepartmentStoreWithDateRange(
                $storeId,
                $request->input('start_date'),
                $request->input('end_date')
            );
            
            if ($sales->isEmpty()) {
                return $this->successResponse(
                    [],
                    'No hay ventas registradas para los filtros especificados'
                );
            }

            return $this->successResponse(
                DepartmentSalesResource::collection($sales),
                'Ventas por departamento obtenidas correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error al obtener las ventas: ' . $e->getMessage(),
                500
            );
        }
    }

    public function getTransactionsByDepartment($departmentId): JsonResponse
    {
        try {
            $department = Department::find($departmentId);
            if (!$department) {
                return $this->errorResponse(
                    'El departamento no existe',
                    404
                );
            }

            $transactions = $this->salesRepository->getTransactionsByDepartment($departmentId);
            
            if ($transactions->isEmpty()) {
                return $this->successResponse(
                    [],
                    'No hay transacciones registradas para este departamento'
                );
            }

            return $this->successResponse(
                TransactionResource::collection($transactions),
                'Transacciones del departamento obtenidas correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error al obtener las transacciones: ' . $e->getMessage(),
                500
            );
        }
    }
}
