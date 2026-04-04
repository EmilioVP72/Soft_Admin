<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::get('/me', [AuthController::class, 'me'])->name('me');
    });
});

use App\Http\Controllers\Api\ReportController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

// Rutas de Reportes
Route::prefix('reports')->group(function () {
    Route::get('/sales/general', [ReportController::class, 'salesGeneral']);
    Route::get('/sales/store/{store_id}', [ReportController::class, 'salesStore']);
    Route::get('/employees', [ReportController::class, 'employees']);
    Route::get('/stores', [ReportController::class, 'stores']);
});


Route::middleware('auth:api')->group(function () {
    // Rutas de Ventas/Sales
    Route::prefix('sales')->group(function () {
        require __DIR__ . '/Sale/sales.php';
    });

    // Rutas de Tiendas/Store
    Route::prefix('stores')->group(function () {
        require __DIR__ . '/Store/stores.php';
    });

    // Rutas de Empleados/Employees
    Route::prefix('employees')->group(function () {
        require __DIR__ . '/Employee/employees.php';
    });

    // Rutas de Localidades/Localities
    Route::prefix('localities')->group(function () {
        require __DIR__ . '/Locality/localities.php';
    });

    // Rutas de Proveedores/Suppliers
    Route::prefix('suppliers')->group(function () {
        require __DIR__ . '/Supplier/suppliers.php';
    });

    // Rutas de Entradas/Inputs
    Route::prefix('inputs')->group(function () {
        require __DIR__ . '/Input/inputs.php';
    });

    // Rutas de Salidas/Outputs
    Route::prefix('outputs')->group(function () {
        require __DIR__ . '/Output/outputs.php';
    });

    // Rutas de Metodos de Pago/Payments
    Route::prefix('payments')->group(function () {
        require __DIR__ . '/Payment/payments.php';
    });

    // Rutas de Departamentos Generales/General Departments
    Route::prefix('general-departments')->group(function () {
        require __DIR__ . '/GeneralDepartment/general_departments.php';
    });

    // Rutas de Departamentos por Tienda/Store Departments
    Route::prefix('departments')->group(function () {
        require __DIR__ . '/Department/departments.php';
    });
});