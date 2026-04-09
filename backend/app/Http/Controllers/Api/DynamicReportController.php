<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\View;
use App\Services\DynamicReportExcelService;

class DynamicReportController extends Controller
{
    /**
     * Reusable method to generate and download PDF
     */
    private function generatePdf($html, $filename, $landscape = false)
    {
        $browser = Browsershot::html($html)
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
            ->margins(10, 10, 10, 10);
            
        if ($landscape) {
            $browser->landscape();
        }

        $pdf = $browser->base64pdf();

        return response()->streamDownload(function () use ($pdf) {
            echo base64_decode($pdf);
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    public function promotionsPdf(Request $request)
    {
        $items = $request->input('items', []);
        
        $html = View::make('reports.promotions', [
            'empresa_nombre' => 'Reporte Dinámico',
            'titulo' => 'Calculadora de Promociones',
            'items' => $items
        ])->render();

        return $this->generatePdf($html, 'reporte_promociones.pdf');
    }

    public function promotionsExcel(Request $request, DynamicReportExcelService $excelService)
    {
        $items = $request->input('items', []);
        $spreadsheet = $excelService->generatePromotionsExcel($items);
        $filename = 'Calculadora_Promociones_' . date('Y-m-d_His') . '.xlsx';
        
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function suppliersPdf(Request $request)
    {
        $items = $request->input('items', []);
        
        $html = View::make('reports.suppliers', [
            'empresa_nombre' => 'Reporte Contable',
            'titulo' => 'Directorio de Proveedores',
            'items' => $items
        ])->render();

        return $this->generatePdf($html, 'reporte_proveedores.pdf', true);
    }

    public function suppliersExcel(Request $request, DynamicReportExcelService $excelService)
    {
        $items = $request->input('items', []);
        $spreadsheet = $excelService->generateSuppliersExcel($items);
        $filename = 'Proveedores_' . date('Y-m-d_His') . '.xlsx';
        
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function departmentsPdf(Request $request)
    {
        $items = $request->input('items', []);
        $storeName = $request->input('storeName', 'Todas las Sucursales');
        
        $html = View::make('reports.departments', [
            'empresa_nombre' => 'Sucursal: ' . $storeName,
            'titulo' => 'Directorio de Departamentos',
            'items' => $items
        ])->render();

        return $this->generatePdf($html, 'reporte_departamentos.pdf', false);
    }

    public function departmentsExcel(Request $request, DynamicReportExcelService $excelService)
    {
        $items = $request->input('items', []);
        $storeName = $request->input('storeName', 'Todas las Sucursales');
        
        $spreadsheet = $excelService->generateDepartmentsExcel($items, $storeName);
        $filename = 'Departamentos_' . date('Y-m-d_His') . '.xlsx';
        
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
