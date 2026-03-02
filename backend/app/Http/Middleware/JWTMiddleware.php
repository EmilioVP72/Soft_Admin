<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (!JWTAuth::parseToken()->authenticate()) {
                return response()->json(
                    [
                        'flag' => false,
                        'code' => 401,
                        'message' => 'Usuario no autenticado',
                        'data' => []
                    ],
                    401
                );
            }
        } catch (TokenExpiredException $e) {
            return response()->json(
                [
                    'flag' => false,
                    'code' => 401,
                    'message' => 'Token expirado',
                    'data' => []
                ],
                401
            );
        } catch (JWTException $e) {
            return response()->json(
                [
                    'flag' => false,
                    'code' => 401,
                    'message' => 'Token inválido',
                    'data' => []
                ],
                401
            );
        }

        return $next($request);
    }
}
