<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\EmployeeController;

/*
|--------------------------------------------------------------------------
| API Routes Employees (Empleados)
|--------------------------------------------------------------------------
|
| RESTful CRUD Routes for Employee Management
*/

Route::middleware('auth:api')->group(function () {
    // GET - Obtener todos los empleados
    Route::get('/all', [EmployeeController::class, 'index']);

    // GET - Obtener solo empleados activos
    Route::get('/active', [EmployeeController::class, 'getActive']);

    // GET - Obtener empleados de una tienda
    Route::get('/store/{storeId}', [EmployeeController::class, 'getByStore']);

    // GET - Obtener empleados por posición
    Route::get('/position/{position}', [EmployeeController::class, 'getByPosition']);

    // GET - Buscar empleados (por nombre, email o documento)
    Route::get('/search', [EmployeeController::class, 'search']);

    // GET - Obtener empleados eliminados (soft deleted)
    Route::get('/trashed', [EmployeeController::class, 'getTrashed']);

    // GET - Obtener un empleado específico
    Route::get('/{id}', [EmployeeController::class, 'show']);

    // POST - Crear un nuevo empleado
    Route::post('/create', [EmployeeController::class, 'store']);

    // PUT - Actualizar un empleado
    Route::put('/update/{id}', [EmployeeController::class, 'update']);

    // DELETE - Eliminar un empleado (soft delete)
    Route::delete('/delete/{id}', [EmployeeController::class, 'destroy']);

    // PUT - Restaurar un empleado eliminado
    Route::put('/restore/{id}', [EmployeeController::class, 'restore']);

    // DELETE - Eliminar permanentemente un empleado
    Route::delete('/force-delete/{id}', [EmployeeController::class, 'forceDelete']);
});
