<?php

namespace App\Traits;

trait UtilResponse
{
    public function successResponse($data = [], $message = 'Success Operation', $code = 200)
    {
        return response()->json(
            [
                "flag" => true,
                "code" => $code,
                "message" => $message,
                "data" => $data
            ],
            $code
        );
    }

    public function errorResponse($message = 'Error Ocurred', $code = 404, $data = [])
    {
        return response()->json(
            [
                "flag" => false,
                'code' => $code,
                "message" => $message,
                "data" => $data
            ],
            $code
        );
    }

    public function validationErrorResponse($errors = [])
    {
        return response()->json(
            [
                "flag" => false,
                'code' => 422,
                "message" => "Validation error",
                "data" => $errors
            ],
            422
        );
    }

    /**
     * Respuesta no autenticado
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function unauthenticatedResponse($message = 'Unauthenticated')
    {
        return response()->json(
            [
                "flag" => false,
                'code' => 401,
                "message" => $message,
                "data" => []
            ],
            401
        );
    }
}