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
use App\Services\SalesReportExcelService;
use App\Services\GeneralReportExcelService;
use App\Services\EmployeeReportExcelService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

        $ventas = Transaction::with(['details.department'])
                    ->where('transaction_type', 'sale')
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->get();
        
        $deptMap = [];
        foreach ($ventas as $sale) {
            if ($sale->details) {
                foreach ($sale->details as $detail) {
                    $deptName = $detail->department ? $detail->department->department : 'General';
                    if (!isset($deptMap[$deptName])) {
                        $deptMap[$deptName] = [
                            'name' => $deptName,
                            'quantity' => 0,
                            'total' => 0
                        ];
                    }
                    $deptMap[$deptName]['quantity'] += (float)$detail->quantity;
                    $deptMap[$deptName]['total'] += (float)$detail->subtotal;
                }
            }
        }

        $totalRecaudado = array_sum(array_column($deptMap, 'total'));
        $operacionesCount = array_sum(array_column($deptMap, 'quantity')); // En general sumamos cantidad
        $ticketPromedio = 0; // Not applicable for department grouping directly, or overall avg

        $html = View::make('reports.ventas_generales', [
            'empresa_nombre' => 'Reportes Corporativos',
            'empresa_direccion' => 'Reporte General de Operaciones',
            'titulo' => 'Ventas Generales',
            'fecha_inicio' => $startDate,
            'fecha_fin' => $endDate,
            'departamentos' => $deptMap,
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
                    ->where('transaction_type', 'sale')
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
     * Download sales report by store in Excel
     */
    public function salesStoreExcel(Request $request, $storeId, SalesReportExcelService $excelService)
    {
        try {
            [$start, $end] = $this->getDateLimits($request);
            $filters = [
                'start_date' => $start->format('Y-m-d'),
                'end_date' => $end->format('Y-m-d')
            ];

            $spreadsheet = $excelService->generateStoresSalesExcel($storeId, $filters);
            
            $store = Store::find($storeId);
            $storeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $store ? $store->store : 'Desconocida');
            $filename = 'Ventas_Sucursal_' . $storeName . '_' . date('Y-m-d_His') . '.xlsx';
            
            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error generating Excel: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Download general sales report in Excel
     */
    public function salesGeneralExcel(Request $request, GeneralReportExcelService $excelService)
    {
        try {
            [$start, $end] = $this->getDateLimits($request);
            $filters = [
                'start_date' => $start->format('Y-m-d'),
                'end_date' => $end->format('Y-m-d')
            ];

            $spreadsheet = $excelService->generateGeneralSalesExcel($filters);
            $filename = 'Ventas_Generales_' . date('Y-m-d_His') . '.xlsx';
            
            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Download employee list report in Excel
     */
    public function employeesExcel(Request $request, EmployeeReportExcelService $excelService)
    {
        try {
            $spreadsheet = $excelService->generateEmployeeExcel();
            $filename = 'Reporte_Empleados_' . date('Y-m-d_His') . '.xlsx';
            
            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 400);
        }
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
        $sucursales = Store::with('locality')->orderBy('store')->get();
        
        $totalSucursales = $sucursales->count();

        $html = View::make('reports.sucursales', [
            'sucursales' => $sucursales,
            'total_sucursales' => $totalSucursales
        ])->render();

        return $this->generatePdf($html, 'reporte_sucursales.pdf');
    }

    public function inputsPdf(Request $request)
    {
        [$startDate, $endDate] = $this->getDateLimits($request);

        $movimientos = Transaction::with(['store', 'user', 'payment'])
                    ->where('transaction_type', 'input')
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date', 'asc')
                    ->get();
        
        $montoTotal = $movimientos->sum('total_amount');
        $operacionesCount = $movimientos->count();

        $html = View::make('reports.entradas', [
            'fecha_inicio' => $startDate,
            'fecha_fin' => $endDate,
            'movimientos' => $movimientos,
            'monto_total' => $montoTotal,
            'total_operaciones' => $operacionesCount
        ])->render();

        return $this->generatePdf($html, 'reporte_entradas.pdf');
    }

    public function inputsExcel(Request $request)
    {
        [$startDate, $endDate] = $this->getDateLimits($request);
        $inputs = Transaction::with(['store', 'user', 'payment'])
                    ->where('transaction_type', 'input')
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date', 'asc')
                    ->get();
                    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Entradas');
        
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Sucursal');
        $sheet->setCellValue('C1', 'Usuario');
        $sheet->setCellValue('D1', 'Pago');
        $sheet->setCellValue('E1', 'Monto Total');
        $sheet->setCellValue('F1', 'Notas');
        $sheet->setCellValue('G1', 'Fecha');

        $row = 2;
        foreach ($inputs as $input) {
            $sheet->setCellValue('A' . $row, $input->id_transaction);
            $sheet->setCellValue('B' . $row, $input->store->store ?? 'N/A');
            $sheet->setCellValue('C' . $row, $input->user->name ?? 'N/A');
            $sheet->setCellValue('D' . $row, $input->payment->payment ?? 'N/A');
            $sheet->setCellValue('E' . $row, $input->total_amount);
            $sheet->setCellValue('F' . $row, $input->notes ?? '');
            $sheet->setCellValue('G' . $row, \Carbon\Carbon::parse($input->transaction_date)->format('Y-m-d H:i'));
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'reporte_entradas.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function outputsPdf(Request $request)
    {
        [$startDate, $endDate] = $this->getDateLimits($request);

        $movimientos = Transaction::with(['store', 'user', 'payment'])
                    ->where('transaction_type', 'output')
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date', 'asc')
                    ->get();
        
        $montoTotal = $movimientos->sum('total_amount');
        $operacionesCount = $movimientos->count();

        $html = View::make('reports.salidas', [
            'fecha_inicio' => $startDate,
            'fecha_fin' => $endDate,
            'movimientos' => $movimientos,
            'monto_total' => $montoTotal,
            'total_operaciones' => $operacionesCount
        ])->render();

        return $this->generatePdf($html, 'reporte_salidas.pdf');
    }

    public function outputsExcel(Request $request)
    {
        [$startDate, $endDate] = $this->getDateLimits($request);
        $outputs = Transaction::with(['store', 'user', 'payment'])
                    ->where('transaction_type', 'output')
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date', 'asc')
                    ->get();
                    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Salidas');
        
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Sucursal');
        $sheet->setCellValue('C1', 'Usuario');
        $sheet->setCellValue('D1', 'Pago');
        $sheet->setCellValue('E1', 'Monto Total');
        $sheet->setCellValue('F1', 'Notas');
        $sheet->setCellValue('G1', 'Fecha');

        $row = 2;
        foreach ($outputs as $output) {
            $sheet->setCellValue('A' . $row, $output->id_transaction);
            $sheet->setCellValue('B' . $row, $output->store->store ?? 'N/A');
            $sheet->setCellValue('C' . $row, $output->user->name ?? 'N/A');
            $sheet->setCellValue('D' . $row, $output->payment->payment ?? 'N/A');
            $sheet->setCellValue('E' . $row, $output->total_amount);
            $sheet->setCellValue('F' . $row, $output->notes ?? '');
            $sheet->setCellValue('G' . $row, \Carbon\Carbon::parse($output->transaction_date)->format('Y-m-d H:i'));
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'reporte_salidas.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Reusable method to generate and download PDF
     */
     private function generatePdf($html, $filename)
    {
        $pdf = Browsershot::html($html)
            ->setChromePath('/usr/bin/chromium')
            ->noSandbox()
            ->addChromiumArguments([
                'disable-gpu',
                'disable-dev-shm-usage',
                'disable-software-rasterizer',
                'disable-extensions',
                'no-first-run',
                'no-default-browser-check',
		'crash-dumps-dir=/tmp/chromium-crashes',
                'user-data-dir=/tmp/chrome-user-data',
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
