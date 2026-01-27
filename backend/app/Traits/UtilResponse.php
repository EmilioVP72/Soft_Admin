<?php

namespace App\Traits;

class UtilResponse
{
    function succesResponse($data = [], $message = 'Success Operation', $code = 200)
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

    function errorResponse($message = 'Error Ocurred', $code = 404)
    {
        return response()->json(
            [
                "flag" => false,
                'code' => $code,
                "message" => $message,
                "data" => []
            ],
            $code
        );
    }
}