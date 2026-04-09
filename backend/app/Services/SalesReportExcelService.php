<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Chart\BarChart;
use PhpOffice\PhpSpreadsheet\Chart\PieChart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\Reference;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use App\Models\Transaction;
use App\Models\Store;
use Carbon\Carbon;

class SalesReportExcelService
{
    private $cacheDir;
    private $cacheTTL = 3600; // 1 hora
    
    public function __construct()
    {
        $this->cacheDir = storage_path('app/excel_reports');
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    public function generateStoresSalesExcel($storeId, $filters = [])
    {
        if (!$this->validateInput($storeId, $filters)) {
            throw new \Exception('Datos inválidos para generar el reporte');
        }
        
        $salesData = $this->getSalesDataByDepartment($storeId, $filters);
        
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        
        if (empty($salesData['departments'])) {
            // Default empty sheet
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Sin Datos');
            $sheet->setCellValue('A1', 'No se encontraron registros de ventas para la tienda en las fechas seleccionadas.');
            return $spreadsheet;
        }
        
        foreach ($salesData['departments'] as $index => $department) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle(substr(str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', $department['name']), 0, 31));
            
            $this->fillDepartmentSheet($sheet, $department, $salesData);
            $this->addDepartmentChart($sheet, $department);
        }
        
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Resumen');
        $this->fillSummarySheet($summarySheet, $salesData);
        $this->addSummaryChart($summarySheet, $salesData);
        
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }
    
    private function validateInput($storeId, $filters)
    {
        if (empty($storeId) || !is_numeric($storeId)) {
            return false;
        }
        return true;
    }
    
    private function getSalesDataByDepartment($storeId, $filters = [])
    {
        $query = Transaction::where('fk1_id_store', $storeId)->where('transaction_type', 'sale');
        
        if (!empty($filters['start_date'])) {
            $query->where('transaction_date', '>=', Carbon::parse($filters['start_date'])->startOfDay());
        }
        if (!empty($filters['end_date'])) {
            $query->where('transaction_date', '<=', Carbon::parse($filters['end_date'])->endOfDay());
        }
        
        $sales = $query->with(['details.department', 'store', 'user', 'payment'])->get();
        $store = Store::find($storeId);
        
        $salesData = [
            'storeName' => $store ? $store->store : 'N/A',
            'storeId' => $storeId,
            'startDate' => $filters['start_date'] ?? null,
            'endDate' => $filters['end_date'] ?? null,
            'departments' => []
        ];
        
        $departmentMap = [];
        
        foreach ($sales as $sale) {
            if ($sale->details && count($sale->details) > 0) {
                foreach ($sale->details as $detail) {
                    $deptName = $detail->department ? $detail->department->department : 'General';
                    
                    if (!isset($departmentMap[$deptName])) {
                        $departmentMap[$deptName] = [
                            'name' => $deptName,
                            'sales' => [],
                            'total_quantity' => 0,
                            'total_amount' => 0
                        ];
                    }
                    
                    $saleRecord = [
                        'quantity' => $detail->quantity ?? 0,
                        'unit_price' => $detail->unit_price ?? 0,
                        'subtotal' => $detail->subtotal ?? 0,
                        'transaction_date' => $sale->transaction_date ? Carbon::parse($sale->transaction_date)->format('Y-m-d H:i:s') : 'N/A',
                        'user_name' => $sale->user ? $sale->user->full_name : 'N/A',
                        'total_amount' => $detail->subtotal ?? ($sale->total_amount ?? 0), // Use subtotal per department logic
                        'payment_method' => $sale->payment ? $sale->payment->method : 'N/A'
                    ];
                    
                    $departmentMap[$deptName]['sales'][] = $saleRecord;
                    $departmentMap[$deptName]['total_quantity'] += (float) ($detail->quantity ?? 0);
                    $departmentMap[$deptName]['total_amount'] += (float) ($detail->subtotal ?? 0);
                }
            }
        }
        
        $salesData['departments'] = array_values($departmentMap);
        return $salesData;
    }

    private function fillDepartmentSheet($sheet, $department, $salesData)
    {
        $row = 1;
        $sheet->setCellValue('A' . $row, 'VENTAS - ' . strtoupper($department['name']));
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');
        $row += 2;
        
        $sheet->setCellValue('A' . $row, 'Sucursal:');
        $sheet->setCellValue('B' . $row, $salesData['storeName']);
        $row++;
        $sheet->setCellValue('A' . $row, 'Departamento:');
        $sheet->setCellValue('B' . $row, $department['name']);
        $row += 2;
        
        $headers = ['Cantidad Vendida', 'Precio Unitario', 'Subtotal', 'Fecha de la Venta', 'Usuario', 'Total Ticket', 'Método de Pago'];
        $col = 1;
        foreach ($headers as $header) {
            $sheet->setCellValue([$col, $row], $header);
            $col++;
        }
        
        $headerRange = 'A' . $row . ':G' . $row;
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setARGB(Color::COLOR_WHITE);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4472C4');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal('center')->setVertical('center');
        $row++;
        
        foreach ($department['sales'] as $sale) {
            $sheet->setCellValue([1, $row], $sale['quantity']);
            $sheet->setCellValue([2, $row], $sale['unit_price']);
            $sheet->setCellValue([3, $row], $sale['subtotal']);
            $sheet->setCellValue([4, $row], $sale['transaction_date']);
            $sheet->setCellValue([5, $row], $sale['user_name']);
            $sheet->setCellValue([6, $row], $sale['total_amount']);
            $sheet->setCellValue([7, $row], $sale['payment_method']);
            
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
            $row++;
        }
        
        $sheet->setCellValue('A' . $row, 'TOTAL:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->setCellValue('A' . ($row + 1), 'Total Cantidad:');
        $sheet->setCellValue('B' . ($row + 1), $department['total_quantity']);
        $sheet->getStyle('B' . ($row + 1))->getFont()->setBold(true);
        $sheet->setCellValue('A' . ($row + 2), 'Total Ventas:');
        $sheet->setCellValue('B' . ($row + 2), $department['total_amount']);
        $sheet->getStyle('B' . ($row + 2))->getFont()->setBold(true);
        $sheet->getStyle('B' . ($row + 2))->getNumberFormat()->setFormatCode('$#,##0.00');
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(18);
    }

    private function fillSummarySheet($sheet, $salesData)
    {
        $row = 1;
        $sheet->setCellValue('A' . $row, 'RESUMEN DE VENTAS POR DEPARTAMENTO');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');
        $row += 2;
        
        $sheet->setCellValue('A' . $row, 'Sucursal: ' . $salesData['storeName']);
        $row += 2;
        
        $sheet->setCellValue('A' . $row, 'Departamento');
        $sheet->setCellValue('B' . $row, 'Total Cantidad');
        $sheet->setCellValue('C' . $row, 'Total Ventas');
        $sheet->setCellValue('D' . $row, '# Transacciones');
        
        $headerRange = 'A' . $row . ':D' . $row;
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setARGB(Color::COLOR_WHITE);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF70AD47');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal('center');
        $row++;
        
        $totalQty = 0;
        $totalAmount = 0;
        
        foreach ($salesData['departments'] as $department) {
            $sheet->setCellValue('A' . $row, $department['name']);
            $sheet->setCellValue('B' . $row, $department['total_quantity']);
            $sheet->setCellValue('C' . $row, $department['total_amount']);
            $sheet->setCellValue('D' . $row, count($department['sales']));
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
            $totalQty += $department['total_quantity'];
            $totalAmount += $department['total_amount'];
            $row++;
        }
        
        $sheet->setCellValue('A' . $row, 'TOTAL GENERAL');
        $sheet->setCellValue('B' . $row, $totalQty);
        $sheet->setCellValue('C' . $row, $totalAmount);
        
        $totalRange = 'A' . $row . ':D' . $row;
        $sheet->getStyle($totalRange)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
        
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(18);
    }
    
    private function addDepartmentChart($sheet, $department)
    {
        if (count($department['sales']) == 0) {
            return;
        }
        
        $dataRow = 7; // El encabezado termina en la fila 6
        $lastRow = $dataRow + count($department['sales']) - 1;
        
        // Simplemente añadiremos etiquetas a las fechas o índices para la categoría
        $categories = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $sheet->getTitle() . '!$D$' . $dataRow . ':$D$' . $lastRow, null, count($department['sales']))];
        $values = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $sheet->getTitle() . '!$C$' . $dataRow . ':$C$' . $lastRow, null, count($department['sales']))];
        
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART, 
            DataSeries::GROUPING_STANDARD, 
            range(0, count($values) - 1), 
            [], 
            $categories, 
            $values
        );
        $series->setPlotDirection(DataSeries::DIRECTION_COL);
        
        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title('Subtotal por Transacción');
        
        $chart = new Chart('VentasDept', $title, $legend, $plotArea, true, 0, null, null);
        $chart->setTopLeftPosition('I' . $dataRow);
        $chart->setBottomRightPosition('Q' . ($lastRow + 10));
        
        $sheet->addChart($chart);
    }
    
    private function addSummaryChart($sheet, $salesData)
    {
        if (count($salesData['departments']) == 0) {
            return;
        }
        
        $dataRow = 6; 
        $lastRow = $dataRow + count($salesData['departments']) - 1;
        
        $categories = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Resumen!$A$' . $dataRow . ':$A$' . $lastRow, null, count($salesData['departments']))];
        $values = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Resumen!$C$' . $dataRow . ':$C$' . $lastRow, null, count($salesData['departments']))];
        
        $series = new DataSeries(
            DataSeries::TYPE_PIECHART, 
            null, 
            range(0, count($values) - 1), 
            [], 
            $categories, 
            $values
        );
        
        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title('Distribución de Ingresos');
        
        $chart = new Chart('DistribucionDept', $title, $legend, $plotArea, true, 0, null, null);
        $chart->setTopLeftPosition('F' . $dataRow);
        $chart->setBottomRightPosition('M' . ($lastRow + 15));
        
        $sheet->addChart($chart);
    }
}
