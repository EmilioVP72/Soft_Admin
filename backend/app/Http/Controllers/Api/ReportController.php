<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Store;
use App\Models\Employee;
use Spatie\Browsershot\Browsershot;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class ReportController extends Controller
{
    /**
     * Parse date limits logic based on request
     */
    private function getDateLimits(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$startDate || !$endDate) {
            // Default to last month if not specified fully
            $start = Carbon::now()->subMonth()->startOfMonth();
            $end = Carbon::now()->subMonth()->endOfMonth();
            
            // if only start_date is missing, but end is present, we adjust
            if ($endDate) {
                $end = Carbon::parse($endDate)->endOfDay();
                $start = $end->copy()->subMonth()->startOfMonth();
            }
            // if only end_date is missing, we adjust
            if ($startDate) {
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::now();
            }
        } else {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        }

        return [$start, $end];
    }

    /**
     * Download general sales report
     */
    public function salesGeneral(Request $request)
    {
        [$startDate, $endDate] = $this->getDateLimits($request);

        $ventas = Transaction::with(['store', 'payment'])
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date', 'asc')
                    ->get();
        
        $totalRecaudado = $ventas->sum('total_amount');
        $operacionesCount = $ventas->count();
        $ticketPromedio = $operacionesCount > 0 ? $totalRecaudado / $operacionesCount : 0;

        $html = View::make('reports.ventas', [
            'empresa_nombre' => 'Reportes Corporativos',
            'empresa_direccion' => 'Reporte General de Operaciones',
            'titulo' => 'Ventas Generales',
            'fecha_inicio' => $startDate,
            'fecha_fin' => $endDate,
            'ventas' => $ventas,
            'total_recaudado' => $totalRecaudado,
            'operaciones_count' => $operacionesCount,
            'ticket_promedio' => $ticketPromedio
        ])->render();

        return $this->generatePdf($html, 'reporte_ventas_general.pdf');
    }

    /**
     * Download sales report by store
     */
    public function salesStore(Request $request, $storeId)
    {
        $store = Store::findOrFail($storeId);
        [$startDate, $endDate] = $this->getDateLimits($request);

        $ventas = Transaction::with(['store', 'payment'])
                    ->where('fk1_id_store', $storeId)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date', 'asc')
                    ->get();
        
        $totalRecaudado = $ventas->sum('total_amount');
        $operacionesCount = $ventas->count();
        $ticketPromedio = $operacionesCount > 0 ? $totalRecaudado / $operacionesCount : 0;

        $html = View::make('reports.ventas', [
            'empresa_nombre' => 'Sucursal: ' . $store->store,
            'empresa_direccion' => $store->street . ' #' . $store->exterior_number . ', ' . $store->colony,
            'titulo' => 'Ventas por Tienda',
            'fecha_inicio' => $startDate,
            'fecha_fin' => $endDate,
            'ventas' => $ventas,
            'total_recaudado' => $totalRecaudado,
            'operaciones_count' => $operacionesCount,
            'ticket_promedio' => $ticketPromedio
        ])->render();

        return $this->generatePdf($html, 'reporte_ventas_tienda_' . $storeId . '.pdf');
    }

    /**
     * Download employee list report
     */
    public function employees(Request $request)
    {
        $empleados = Employee::with('store')->orderBy('full_name')->get();
        
        $totalEmpleados = $empleados->count();
        $activosCount = $empleados->where('status', 'Active')->count();
        $sucursalesCount = $empleados->pluck('fk_id_store')->unique()->filter()->count();

        $html = View::make('reports.empleados', [
            'empleados' => $empleados,
            'total_empleados' => $totalEmpleados,
            'activos_count' => $activosCount,
            'sucursales_count' => $sucursalesCount
        ])->render();

        return $this->generatePdf($html, 'reporte_empleados.pdf');
    }

    /**
     * Download stores list report
     */
    public function stores(Request $request)
    {
        $sucursales = Store::with('locality')->orderBy('name')->get();
        
        $totalSucursales = $sucursales->count();

        $html = View::make('reports.sucursales', [
            'sucursales' => $sucursales,
            'total_sucursales' => $totalSucursales
        ])->render();

        return $this->generatePdf($html, 'reporte_sucursales.pdf');
    }

    /**
     * Reusable method to generate and download PDF
     */
    private function generatePdf($html, $filename)
    {
        $pdf = Browsershot::html($html)
            ->setChromePath('/usr/bin/chromium-browser')
            ->noSandbox()
            ->addChromiumArguments([
                'disable-gpu',
                'disable-dev-shm-usage',
                'disable-software-rasterizer',
                'disable-extensions'
            ])
            ->timeout(120)
            ->format('A4')
            ->margins(10, 10, 10, 10)
            ->base64pdf();

        return response()->streamDownload(function () use ($pdf) {
            echo base64_decode($pdf);
        }, $filename, ['Content-Type' => 'application/pdf']);
    }
}
