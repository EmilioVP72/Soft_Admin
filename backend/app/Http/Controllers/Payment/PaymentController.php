<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Payment\PaymentRepository;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Requests\Payment\UpdatePaymentRequest;
use App\Http\Resources\Payment\PaymentResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    use UtilResponse;
    private $paymentRepository;

    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function index(): JsonResponse 
    {
        try {
            $payments = $this->paymentRepository->getAll();
            return $this->successResponse(
                PaymentResource::collection($payments), 
                'Métodos de pago obtenidos correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener métodos de pago: ' . $e->getMessage(), 500);
        }
    }

    public function show($id): JsonResponse 
    {
        try {
            $payment = $this->paymentRepository->find($id);
            if (!$payment) {
                return $this->errorResponse('Método de pago no encontrado', 404);
            }
            return $this->successResponse(
                new PaymentResource($payment), 
                'Método de pago obtenido correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener el método de pago: ' . $e->getMessage(), 500);
        }
    }

    public function store(StorePaymentRequest $request): JsonResponse 
    {
        try {
            $payment = $this->paymentRepository->create($request->validated());
            return $this->successResponse(
                new PaymentResource($payment), 
                'Método de pago creado correctamente', 
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al crear el método de pago: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdatePaymentRequest $request, $id): JsonResponse 
    {
        try {
            $payment = $this->paymentRepository->find($id);
            if (!$payment) {
                return $this->errorResponse('Método de pago no encontrado', 404);
            }
            $payment = $this->paymentRepository->update($payment, $request->validated());
            return $this->successResponse(
                new PaymentResource($payment), 
                'Método de pago actualizado correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al actualizar el método de pago: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id): JsonResponse 
    {
        try {
            $payment = $this->paymentRepository->find($id);
            if (!$payment) {
                return $this->errorResponse('Método de pago no encontrado', 404);
            }
            $this->paymentRepository->delete($payment);
            return $this->successResponse(null, 'Método de pago eliminado correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse('Error al eliminar el método de pago: ' . $e->getMessage(), 500);
        }
    }
}
