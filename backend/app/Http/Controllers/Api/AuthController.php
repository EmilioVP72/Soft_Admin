<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\UtilResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    use UtilResponse;

    /**
     * Registrar un nuevo usuario
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'user' => $request->user,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone ?? null,
            ]);

            $token = JWTAuth::fromUser($user);

            return $this->successResponse(
                [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60
                ],
                'Usuario registrado exitosamente',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error al registrar usuario: ' . $e->getMessage(),
                500
            );
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->errorResponse('Las credenciales son incorrectas', 401);
            }

            $user = JWTAuth::user();

            if (!$user) {
                return $this->errorResponse('Usuario no encontrado tras autenticación', 404);
            }

            return $this->successResponse([
                'user' => new UserResource($user), 
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60
            ], 'Autenticación exitosa', 200);

        } catch (JWTException $e) {
            return $this->errorResponse(
                'Error al generar el token: ' . $e->getMessage(),
                500
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error en la autenticación: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Logout - Invalidar token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return $this->successResponse(
                [],
                'Sesión cerrada exitosamente',
                200
            );
        } catch (JWTException $e) {
            return $this->errorResponse(
                'Error al cerrar sesión: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Refrescar el token JWT
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());

            return $this->successResponse(
                [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60
                ],
                'Token renovado exitosamente',
                200
            );
        } catch (JWTException $e) {
            return $this->errorResponse(
                'Error al renovar el token: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Obtener el usuario autenticado actual
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try {
            if (!$user = auth('api')->user()) {
                return $this->unauthenticatedResponse('No autenticado');
            }

            return $this->successResponse(
                new UserResource($user),
                'Usuario obtenido exitosamente',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error al obtener usuario: ' . $e->getMessage(),
                500
            );
        }
    }
}
