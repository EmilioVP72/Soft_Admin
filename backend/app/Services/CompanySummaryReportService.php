<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\SupplierPayment;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class CompanySummaryReportService
{
    public function generateOverviewPdfBytes(Carbon $startDate, Carbon $endDate)
    {
        // 1. Fetch Sales (Entradas)
        $ventas = Transaction::with(['store', 'details.department'])
            ->where('transaction_type', 'sale')
            ->whereBetween('transaction_date', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->get();
            
        $entradasTotal = $ventas->sum('total_amount');

        // 2. Fetch Supplier Payments (Salidas)
        $salidas = SupplierPayment::whereBetween('payment_date', [$startDate->startOfDay(), $endDate->endOfDay()])->get();
        $salidasTotal = $salidas->sum('amount_paid');
        
        $balanceGlobal = $entradasTotal - $salidasTotal;

        // 3. Agrupar Ventas por Sucursal
        $ventasPorSucursal = [];
        // 4. Agrupar Ventas por Departamento
        $ventasPorDepartamento = [];

        foreach ($ventas as $sale) {
            // Agrupar sucursales
            $storeName = $sale->store ? $sale->store->store : 'Sin Sucursal';
            if (!isset($ventasPorSucursal[$storeName])) {
                $ventasPorSucursal[$storeName] = 0;
            }
            $ventasPorSucursal[$storeName] += (float)$sale->total_amount;

            // Agrupar departamentos
            if ($sale->details) {
                foreach ($sale->details as $det) {
                    $deptName = $det->department ? $det->department->department : 'General';
                    if (!isset($ventasPorDepartamento[$deptName])) {
                        $ventasPorDepartamento[$deptName] = 0;
                    }
                    $ventasPorDepartamento[$deptName] += (float)$det->subtotal;
                }
            }
        }

        // Ordenar arreglos de mayor a menor ingreso
        arsort($ventasPorSucursal);
        arsort($ventasPorDepartamento);

        // Renderizar vista
        $html = View::make('reports.company_summary', [
            'empresa_nombre' => config('app.name', 'Corporativo'),
            'titulo' => 'Resumen Integral de Operaciones',
            'fecha_inicio' => $startDate,
            'fecha_fin' => $endDate,
            
            'entradas_total' => $entradasTotal,
            'salidas_total' => $salidasTotal,
            'balance_global' => $balanceGlobal,
            
            'ventas_por_sucursal' => $ventasPorSucursal,
            'ventas_por_departamento' => $ventasPorDepartamento
        ])->render();

        try {
            $pdfBase64 = Browsershot::html($html)
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

            return base64_decode($pdfBase64);
        } catch (\Exception $e) {
            \Log::error("Browsershot error on CompanySummaryReportService: " . $e->getMessage());
            // Fallback content in case Browsershot fails (mostly for safe testing environments without chromium)
            return "PDF Generation Failed: " . $e->getMessage();
        }
    }
}
