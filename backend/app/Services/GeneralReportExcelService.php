<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use App\Models\Transaction;
use Carbon\Carbon;

class GeneralReportExcelService
{
    public function generateGeneralSalesExcel($filters = [])
    {
        $query = Transaction::with(['details.department'])->where('transaction_type', 'sale');
        
        if (!empty($filters['start_date'])) {
            $query->where('transaction_date', '>=', Carbon::parse($filters['start_date'])->startOfDay());
        }
        if (!empty($filters['end_date'])) {
            $query->where('transaction_date', '<=', Carbon::parse($filters['end_date'])->endOfDay());
        }
        
        $sales = $query->get();
        if ($sales->count() == 0) {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->removeSheetByIndex(0);
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Ventas Generales');
            $sheet->setCellValue('A1', 'No hay registros de ventas para estas fechas.');
            return $spreadsheet;
        }

        // Agrupar por departamento globalmente
        $deptMap = [];
        foreach ($sales as $sale) {
            if ($sale->details) {
                foreach ($sale->details as $detail) {
                    $deptName = $detail->department ? $detail->department->department : 'General';
                    if (!isset($deptMap[$deptName])) {
                        $deptMap[$deptName] = [
                            'quantity' => 0,
                            'total' => 0
                        ];
                    }
                    $deptMap[$deptName]['quantity'] += (float)$detail->quantity;
                    $deptMap[$deptName]['total'] += (float)$detail->subtotal;
                }
            }
        }
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte Ventas General');
        
        $sheet->setCellValue('A1', 'REPORTE GENERAL DE VENTAS POR DEPARTAMENTO');
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        $sheet->setCellValue('A3', 'Rango de fechas:');
        $sheet->setCellValue('B3', ($filters['start_date'] ?? 'Histórico') . ' a ' . ($filters['end_date'] ?? 'Hoy'));
        
        $row = 5;
        $sheet->setCellValue('A' . $row, 'Departamento');
        $sheet->setCellValue('B' . $row, 'Cantidad Vendida');
        $sheet->setCellValue('C' . $row, 'Ingresos Totales');
        
        $headerRange = 'A' . $row . ':C' . $row;
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setARGB(Color::COLOR_WHITE);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF70AD47');
        
        $row++;
        $grandTotalQty = 0;
        $grandTotalAmount = 0;
        
        foreach ($deptMap as $name => $data) {
            $sheet->setCellValue('A' . $row, $name);
            $sheet->setCellValue('B' . $row, $data['quantity']);
            $sheet->setCellValue('C' . $row, $data['total']);
            
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
            $grandTotalQty += $data['quantity'];
            $grandTotalAmount += $data['total'];
            $row++;
        }
        
        $sheet->setCellValue('A' . $row, 'TOTAL GENERAL:');
        $sheet->setCellValue('B' . $row, $grandTotalQty);
        $sheet->setCellValue('C' . $row, $grandTotalAmount);
        
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
        
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);

        return $spreadsheet;
    }
}
