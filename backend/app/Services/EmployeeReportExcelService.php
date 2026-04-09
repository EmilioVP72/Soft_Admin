<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use App\Models\Employee;

class EmployeeReportExcelService
{
    public function generateEmployeeExcel()
    {
        $empleados = Employee::with('store')->orderBy('full_name')->get();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Empleados');
        
        if ($empleados->count() == 0) {
            $sheet->setCellValue('A1', 'No hay registros de empleados.');
            return $spreadsheet;
        }

        $sheet->setCellValue('A1', 'DIRECTORIO Y NÓMINA DE EMPLEADOS');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        $row = 3;
        $headers = ['Nombre Completo', 'Email', 'Teléfono', 'Sucursal', 'Puesto', 'Salario', 'Estado', 'Fecha Contratación'];
        $col = 1;
        foreach ($headers as $header) {
            $sheet->setCellValue([$col, $row], $header);
            $col++;
        }
        
        $headerRange = 'A' . $row . ':H' . $row;
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setARGB(Color::COLOR_WHITE);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4472C4');
        
        $row++;
        foreach ($empleados as $emp) {
            $sheet->setCellValue([1, $row], $emp->full_name);
            $sheet->setCellValue([2, $row], $emp->email);
            $sheet->setCellValue([3, $row], $emp->phone);
            $sheet->setCellValue([4, $row], $emp->store ? $emp->store->store : 'Sin Asignar');
            $sheet->setCellValue([5, $row], $emp->position);
            $sheet->setCellValue([6, $row], $emp->salary);
            $sheet->setCellValue([7, $row], $emp->status_label);
            $sheet->setCellValue([8, $row], $emp->hire_date ? $emp->hire_date->format('Y-m-d') : 'N/A');
            
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
            $row++;
        }
        
        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setWidth(20);
        }
        $sheet->getColumnDimension('A')->setWidth(30);

        return $spreadsheet;
    }
}
