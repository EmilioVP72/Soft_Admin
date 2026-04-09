<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class DynamicReportExcelService
{
    public function generatePromotionsExcel($items)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Calculadora');

        $sheet->setCellValue('A1', 'REPORTE CALCULADORA DE PROMOCIONES');
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $row = 3;
        $headers = ['Fecha', 'Total Ventas', 'Acumulado'];
        $col = 1;
        foreach ($headers as $header) {
            $sheet->setCellValue([$col, $row], $header);
            $col++;
        }

        $headerRange = 'A' . $row . ':C' . $row;
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setARGB(Color::COLOR_WHITE);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF70AD47');

        $row++;
        if (empty($items)) {
            $sheet->setCellValue('A'.$row, 'No hay datos suministrados');
        } else {
            foreach ($items as $item) {
                $sheet->setCellValue([1, $row], $item['date'] ?? '');
                $sheet->setCellValue([2, $row], $item['totaly_sales'] ?? 0);
                $sheet->setCellValue([3, $row], $item['acumulated_sales'] ?? 0);
                
                $sheet->getStyle('B' . $row . ':C' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
                $row++;
            }
        }

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);

        return $spreadsheet;
    }

    public function generateSuppliersExcel($items)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Proveedores');

        $sheet->setCellValue('A1', 'DIRECTORIO DE PROVEEDORES');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $row = 3;
        $headers = ['ID', 'Proveedor', 'No. Pagos', 'Total Pagado'];
        $col = 1;
        foreach ($headers as $header) {
            $sheet->setCellValue([$col, $row], $header);
            $col++;
        }

        $headerRange = 'A' . $row . ':D' . $row;
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setARGB(Color::COLOR_WHITE);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4472C4');

        $row++;
        if (empty($items)) {
            $sheet->setCellValue('A'.$row, 'No hay datos suministrados');
        } else {
            $totalPagado = 0;
            foreach ($items as $item) {
                $sheet->setCellValue([1, $row], $item['id'] ?? '');
                $sheet->setCellValue([2, $row], $item['name'] ?? '');
                $sheet->setCellValue([3, $row], count($item['payments'] ?? []));
                $amount = (float)($item['totalPaid'] ?? 0);
                $sheet->setCellValue([4, $row], $amount);
                
                $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
                $totalPagado += $amount;
                $row++;
            }
            
            $sheet->setCellValue([3, $row], 'GRAN TOTAL:');
            $sheet->setCellValue([4, $row], $totalPagado);
            $sheet->getStyle('C' . $row . ':D' . $row)->getFont()->setBold(true);
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
        }

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(25);

        return $spreadsheet;
    }

    public function generateDepartmentsExcel($items, $storeName)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Departamentos');

        $sheet->setCellValue('A1', 'DIRECTORIO DE DEPARTAMENTOS');
        $sheet->setCellValue('A2', 'SUCURSAL: ' . mb_strtoupper($storeName));
        $sheet->mergeCells('A1:B1');
        $sheet->mergeCells('A2:B2');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);

        $row = 4;
        $headers = ['ID', 'Nombre del Departamento'];
        $col = 1;
        foreach ($headers as $header) {
            $sheet->setCellValue([$col, $row], $header);
            $col++;
        }

        $headerRange = 'A' . $row . ':B' . $row;
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setARGB(Color::COLOR_WHITE);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4472C4');

        $row++;
        if (empty($items)) {
            $sheet->setCellValue('A'.$row, 'No hay datos suministrados');
        } else {
            foreach ($items as $item) {
                $id = $item['id_department'] ?? $item['id'] ?? '';
                $name = $item['department'] ?? $item['name'] ?? '';
                $sheet->setCellValue([1, $row], $id);
                $sheet->setCellValue([2, $row], $name);
                $row++;
            }
        }

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(50);

        return $spreadsheet;
    }
}
