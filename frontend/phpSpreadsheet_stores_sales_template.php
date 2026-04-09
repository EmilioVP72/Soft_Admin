<?php
/**
 * Plantilla Base para Generar Excel - Ventas por Sucursal
 * 
 * Descripción:
 * Esta plantilla genera un archivo Excel con múltiples tablas, una por cada departamento.
 * A diferencia de la vista que junta todas las tablas en una sola, aquí cada departamento
 * tiene su propia tabla independiente.
 * 
 * Uso en Backend:
 * 1. Recibir los datos de ventas agrupados por departamento del servicio/controller
 * 2. Para cada departamento, crear una nueva hoja o sección en el Excel
 * 3. Aplicar formatos de encabezados y datos según se especifica abajo
 * 
 * Estructura esperada de datos (desde el backend):
 * 
 * $salesData = [
 *     'storeName' => 'Nombre de la Sucursal',
 *     'storeId' => 1,
 *     'startDate' => '2024-01-01',
 *     'endDate' => '2024-12-31',
 *     'paymentMethods' => ['Efectivo', 'Tarjeta'],
 *     'departments' => [
 *         [
 *             'name' => 'Nombre del Departamento',
 *             'sales' => [
 *                 [
 *                     'department' => 'Departamento',
 *                     'quantity' => 10,
 *                     'unit_price' => 100.00,
 *                     'subtotal' => 1000.00,
 *                     'transaction_date' => '2024-01-15 10:30:45',
 *                     'user_name' => 'Nombre Usuario',
 *                     'total_amount' => 1000.00,
 *                     'notes' => 'Notas adicionales',
 *                     'payment_method' => 'Efectivo | Tarjeta | Otro'
 *                 ],
 *                 // ... más registros de venta
 *             ],
 *             'total_quantity' => 100,
 *             'total_amount' => 10000.00
 *         ],
 *         // ... más departamentos
 *     ]
 * ];
 */

// Ejemplo de implementación en el Controller/Service:

/*
 * 
 * use PhpOffice\PhpSpreadsheet\Spreadsheet;
 * use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 * use PhpOffice\PhpSpreadsheet\Style\Font;
 * use PhpOffice\PhpSpreadsheet\Style\Fill;
 * use PhpOffice\PhpSpreadsheet\Style\Alignment;
 * use PhpOffice\PhpSpreadsheet\Style\Border;
 * use PhpOffice\PhpSpreadsheet\Worksheet\Table;
 * use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
 * use PhpOffice\PhpSpreadsheet\Chart\BarChart;
 * use PhpOffice\PhpSpreadsheet\Chart\PieChart;
 * use PhpOffice\PhpSpreadsheet\Chart\Reference;
 * 
 * class SalesReportService
 * {
 *     private $cacheDir = 'storage/cache/excel_reports';
 *     private $cacheTTL = 3600; // 1 hora en segundos
 *     
 *     public function __construct()
 *     {
 *         if (!is_dir($this->cacheDir)) {
 *             mkdir($this->cacheDir, 0755, true);
 *         }
 *     }
 * 
 *     public function generateStoresSalesExcel($storeId, $filters = [])
 *     {
 *         // 1. Validación de datos
 *         if (!$this->validateInput($storeId, $filters)) {
 *             throw new \Exception('Datos inválidos para generar el reporte');
 *         }
 *         
 *         // 2. Verificar caché
 *         $cacheKey = $this->generateCacheKey($storeId, $filters);
 *         $cachedFile = $this->getCachedFile($cacheKey);
 *         if ($cachedFile && file_exists($cachedFile)) {
 *             return $this->serveFile($cachedFile);
 *         }
 *         
 *         // 3. Obtener datos de ventas agrupadas por departamento
 *         $salesData = $this->getSalesDataByDepartment($storeId, $filters);
 *         
 *         // 4. Validar que existan datos
 *         if (empty($salesData['departments'])) {
 *             throw new \Exception('No hay datos de ventas para los filtros especificados');
 *         }
 *         
 *         // 5. Crear spreadsheet
 *         $spreadsheet = new Spreadsheet();
 *         $spreadsheet->removeSheetByIndex(0); // Eliminar hoja por defecto
 *         
 *         // 6. Crear una hoja por cada departamento
 *         foreach ($salesData['departments'] as $index => $department) {
 *             $sheet = $spreadsheet->createSheet();
 *             $sheet->setTitle(substr($department['name'], 0, 31)); // Max 31 caracteres
 *             
 *             // Llenar datos y agregar gráficos
 *             $this->fillDepartmentSheet($sheet, $department, $salesData);
 *             $this->addDepartmentChart($sheet, $department, $index);
 *         }
 *         
 *         // 7. Crear hoja resumen
 *         $summarySheet = $spreadsheet->createSheet();
 *         $summarySheet->setTitle('Resumen');
 *         $this->fillSummarySheet($summarySheet, $salesData);
 *         $this->addSummaryChart($summarySheet, $salesData);
 *         
 *         // 8. Generar archivo
 *         $filename = $this->generateFilename($salesData);
 *         $filepath = $this->cacheDir . '/' . $filename;
 *         
 *         $writer = new Xlsx($spreadsheet);
 *         $writer->save($filepath);
 *         
 *         // 9. Servir archivo
 *         return $this->serveFile($filepath);
 *     }
 *     
 *     /**
 *      * Validar entrada de datos
 *      */
 *     private function validateInput($storeId, $filters)
 *     {
 *         if (empty($storeId) || !is_numeric($storeId)) {
 *             return false;
 *         }
 *         
 *         if (!empty($filters['startDate']) && !empty($filters['endDate'])) {
 *             $startDate = strtotime($filters['startDate']);
 *             $endDate = strtotime($filters['endDate']);
 *             
 *             if ($startDate === false || $endDate === false || $startDate > $endDate) {
 *                 return false;
 *             }
 *         }
 *         
 *         return true;
 *     }
 *     
 *     /**
 *      * Generar clave para caché
 *      */
 *     private function generateCacheKey($storeId, $filters)
 *     {
 *         $key = 'sales_report_' . $storeId;
 *         if (!empty($filters['startDate'])) $key .= '_' . date('Ymd', strtotime($filters['startDate']));
 *         if (!empty($filters['endDate'])) $key .= '_' . date('Ymd', strtotime($filters['endDate']));
 *         if (!empty($filters['paymentMethods'])) {
 *             $key .= '_' . implode('_', array_map('md5', (array)$filters['paymentMethods']));
 *         }
 *         return md5($key);
 *     }
 *     
 *     /**
 *      * Obtener archivo caché si sigue siendo válido
 *      */
 *     private function getCachedFile($cacheKey)
 *     {
 *         $files = glob($this->cacheDir . '/' . $cacheKey . '_*');
 *         
 *         foreach ($files as $file) {
 *             if (time() - filemtime($file) < $this->cacheTTL) {
 *                 return $file;
 *             } else {
 *                 unlink($file); // Eliminar caché expirado
 *             }
 *         }
 *         
 *         return null;
 *     }
 *     
 *     /**
 *      * Limpiar caché antiguo
 *      */
 *     public function cleanCache()
 *     {
 *         $files = glob($this->cacheDir . '/*.xlsx');
 *         
 *         foreach ($files as $file) {
 *             if (time() - filemtime($file) > $this->cacheTTL) {
 *                 unlink($file);
 *             }
 *         }
 *     }
 *     
 *     /**
 *      * Obtener datos de ventas con filtros
 *      */
 *     private function getSalesDataByDepartment($storeId, $filters = [])
 *     {
 *         // Aquí iría la lógica para obtener datos de la BD
 *         // con filtros de rango de fechas y métodos de pago
 *         
 *         $query = Sale::where('store_id', $storeId);
 *         
 *         if (!empty($filters['startDate'])) {
 *             $query->where('transaction_date', '>=', $filters['startDate']);
 *         }
 *         
 *         if (!empty($filters['endDate'])) {
 *             $query->where('transaction_date', '<=', $filters['endDate']);
 *         }
 *         
 *         if (!empty($filters['paymentMethods']) && is_array($filters['paymentMethods'])) {
 *             $query->whereIn('payment_method', $filters['paymentMethods']);
 *         }
 *         
 *         $sales = $query->with('details', 'store', 'user')->get();
 * 
 *         $store = Store::find($storeId);
 *         
 *         $salesData = [
 *             'storeName' => $store ? $store->name : 'N/A',
 *             'storeId' => $storeId,
 *             'startDate' => $filters['startDate'] ?? null,
 *             'endDate' => $filters['endDate'] ?? null,
 *             'departments' => []
 *         ];
 *         
 *         $departmentMap = [];
 *         
 *         foreach ($sales as $sale) {
 *             if ($sale->details && count($sale->details) > 0) {
 *                 foreach ($sale->details as $detail) {
 *                     $deptName = $detail->department ?? 'N/A';
 *                     
 *                     if (!isset($departmentMap[$deptName])) {
 *                         $departmentMap[$deptName] = [
 *                             'name' => $deptName,
 *                             'sales' => [],
 *                             'total_quantity' => 0,
 *                             'total_amount' => 0
 *                         ];
 *                     }
 *                     
 *                     $saleRecord = [
 *                         'quantity' => $detail->quantity ?? 0,
 *                         'unit_price' => $detail->unit_price ?? 0,
 *                         'subtotal' => $detail->subtotal ?? 0,
 *                         'transaction_date' => $sale->transaction_date,
 *                         'user_name' => $sale->user ? $sale->user->name : 'N/A',
 *                         'total_amount' => $sale->total_amount ?? 0,
 *                         'payment_method' => $sale->payment_method ?? 'N/A'
 *                     ];
 *                     
 *                     $departmentMap[$deptName]['sales'][] = $saleRecord;
 *                     $departmentMap[$deptName]['total_quantity'] += $detail->quantity ?? 0;
 *                     $departmentMap[$deptName]['total_amount'] += $sale->total_amount ?? 0;
 *                 }
 *             }
 *         }
 *         
 *         $salesData['departments'] = array_values($departmentMap);
 *         
 *         return $salesData;
 *     }

 *         $this->fillSummarySheet($summarySheet, $salesData);
 *         
 *         // 5. Generar archivo
 *         $writer = new Xlsx($spreadsheet);
 *         $filename = 'Ventas_Sucursal_' . $salesData['storeName'] . '_' . date('Y-m-d_His') . '.xlsx';
 *         
 *         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
 *         header('Content-Disposition: attachment;filename="' . $filename . '"');
 *         
 *         $writer->save('php://output');
 *         exit;
 *     }
 * 
 *     private function fillDepartmentSheet($sheet, $department, $salesData)
 *     {
 *         $row = 1;
 *         
 *         // Título del Departamento
 *         $sheet->setCellValue('A' . $row, 'VENTAS - ' . strtoupper($department['name']));
 *         $sheet->mergeCells('A' . $row . ':G' . $row);
 *         $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
 *         $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');
 *         $row += 2;
 *         
 *         // Información de la sucursal
 *         $sheet->setCellValue('A' . $row, 'Sucursal:');
 *         $sheet->setCellValue('B' . $row, $salesData['storeName']);
 *         $row++;
 *         $sheet->setCellValue('A' . $row, 'Departamento:');
 *         $sheet->setCellValue('B' . $row, $department['name']);
 *         $row += 2;
 *         
 *         // Encabezados de la tabla
 *         $headers = [
 *             'Cantidad Vendida',
 *             'Precio Unitario',
 *             'Subtotal',
 *             'Fecha de la Venta',
 *             'Usuario',
 *             'Total de Ventas',
 *             'Método de Pago'
 *         ];
 *         
 *         $col = 1;
 *         foreach ($headers as $header) {
 *             $sheet->setCellValueByColumnAndRow($col, $row, $header);
 *             $col++;
 *         }
 *         
 *         // Aplicar estilos al encabezado
 *         $headerRange = 'A' . $row . ':G' . $row;
 *         $sheet->getStyle($headerRange)->getFont()->setBold(true)->setColor(new Color('FFFFFF'));
 *         $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4472C4');
 *         $sheet->getStyle($headerRange)->getAlignment()->setHorizontal('center')->setVertical('center');
 *         $row++;
 *         
 *         // Rellenar datos de ventas
 *         foreach ($department['sales'] as $sale) {
 *             $sheet->setCellValueByColumnAndRow(1, $row, $sale['quantity']);
 *             $sheet->setCellValueByColumnAndRow(2, $row, $sale['unit_price']);
 *             $sheet->setCellValueByColumnAndRow(3, $row, $sale['subtotal']);
 *             $sheet->setCellValueByColumnAndRow(4, $row, $sale['transaction_date']);
 *             $sheet->setCellValueByColumnAndRow(5, $row, $sale['user_name']);
 *             $sheet->setCellValueByColumnAndRow(6, $row, $sale['total_amount']);
 *             $sheet->setCellValueByColumnAndRow(7, $row, $sale['payment_method'] ?? 'N/A');
 *             
 *             // Aplicar formatos de números
 *             $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
 *             $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
 *             $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
 *             
 *             $row++;
 *         }
 *         
 *         // Fila de totales
 *         $sheet->setCellValue('A' . $row, 'TOTAL:');
 *         $sheet->getStyle('A' . $row)->getFont()->setBold(true);
 *         
 *         $sheet->setCellValue('A' . ($row + 1), 'Total Cantidad:');
 *         $sheet->setCellValue('B' . ($row + 1), $department['total_quantity']);
 *         $sheet->getStyle('B' . ($row + 1))->getFont()->setBold(true);
 *         
 *         $sheet->setCellValue('A' . ($row + 2), 'Total Ventas:');
 *         $sheet->setCellValue('B' . ($row + 2), $department['total_amount']);
 *         $sheet->getStyle('B' . ($row + 2))->getFont()->setBold(true);
 *         $sheet->getStyle('B' . ($row + 2))->getNumberFormat()->setFormatCode('$#,##0.00');
 *         
 *         // Ajustar ancho de columnas
 *         $sheet->getColumnDimension('A')->setWidth(20);
 *         $sheet->getColumnDimension('B')->setWidth(18);
 *         $sheet->getColumnDimension('C')->setWidth(18);
 *         $sheet->getColumnDimension('D')->setWidth(20);
 *         $sheet->getColumnDimension('E')->setWidth(18);
 *         $sheet->getColumnDimension('F')->setWidth(18);
 *         $sheet->getColumnDimension('G')->setWidth(18);
 *     }
 * 
 *     private function fillSummarySheet($sheet, $salesData)
 *     {
 *         $row = 1;
 *         
 *         // Título
 *         $sheet->setCellValue('A' . $row, 'RESUMEN DE VENTAS POR DEPARTAMENTO');
 *         $sheet->mergeCells('A' . $row . ':D' . $row);
 *         $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
 *         $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');
 *         $row += 2;
 *         
 *         // Información de la sucursal
 *         $sheet->setCellValue('A' . $row, 'Sucursal: ' . $salesData['storeName']);
 *         $row += 2;
 *         
 *         // Encabezados
 *         $sheet->setCellValue('A' . $row, 'Departamento');
 *         $sheet->setCellValue('B' . $row, 'Total Cantidad');
 *         $sheet->setCellValue('C' . $row, 'Total Ventas');
 *         $sheet->setCellValue('D' . $row, '# Transacciones');
 *         
 *         $headerRange = 'A' . $row . ':D' . $row;
 *         $sheet->getStyle($headerRange)->getFont()->setBold(true)->setColor(new Color('FFFFFF'));
 *         $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF70AD47');
 *         $sheet->getStyle($headerRange)->getAlignment()->setHorizontal('center');
 *         $row++;
 *         
 *         // Datos de departamentos
 *         $totalQty = 0;
 *         $totalAmount = 0;
 *         
 *         foreach ($salesData['departments'] as $department) {
 *             $sheet->setCellValue('A' . $row, $department['name']);
 *             $sheet->setCellValue('B' . $row, $department['total_quantity']);
 *             $sheet->setCellValue('C' . $row, $department['total_amount']);
 *             $sheet->setCellValue('D' . $row, count($department['sales']));
 *             
 *             $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
 *             
 *             $totalQty += $department['total_quantity'];
 *             $totalAmount += $department['total_amount'];
 *             $row++;
 *         }
 *         
 *         // Fila de totales finales
 *         $sheet->setCellValue('A' . $row, 'TOTAL GENERAL');
 *         $sheet->setCellValue('B' . $row, $totalQty);
 *         $sheet->setCellValue('C' . $row, $totalAmount);
 *         
 *         $totalRange = 'A' . $row . ':D' . $row;
 *         $sheet->getStyle($totalRange)->getFont()->setBold(true)->setSize(12);
 *         $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
 *         
 *         // Ajustar ancho de columnas
 *         $sheet->getColumnDimension('A')->setWidth(25);
 *         $sheet->getColumnDimension('B')->setWidth(18);
 *         $sheet->getColumnDimension('C')->setWidth(18);
 *         $sheet->getColumnDimension('D')->setWidth(18);
 *     }
 *     
 *     /**
 *      * Agregar gráfico de barras por departamento
 *      */
 *     private function addDepartmentChart($sheet, $department, $index)
 *     {
 *         if (count($department['sales']) == 0) {
 *             return; // No agregar gráfico si no hay datos
 *         }
 *         
 *         $dataRow = 4; // Fila donde comienzan los datos
 *         $lastRow = $dataRow + count($department['sales']);
 *         
 *         // Gráfico de barras - Total de ventas por venta
 *         $barChart = new BarChart();
 *         $barChart->setTitle('Ventas por Transacción - ' . $department['name']);
 *         $barChart->setType(BarChart::SUBTYPE_STANDARD);
 *         
 *         $categories = new Reference($sheet, 'D' . $dataRow, 'D' . $lastRow);
 *         $values = new Reference($sheet, 'F' . $dataRow, 'F' . $lastRow);
 *         
 *         $barChart->addSeries(new DataSeries($categories, null, null, $values));
 *         $barChart->setLegendPosition('r');
 *         
 *         $sheet->addChart($barChart);
 *         
 *         // Posición del gráfico
 *         $barChart->setTopLeftCell('I' . $dataRow);
 *     }
 *     
 *     /**
 *      * Agregar gráfico resumen de ventas por departamento
 *      */
 *     private function addSummaryChart($sheet, $salesData)
 *     {
 *         if (count($salesData['departments']) == 0) {
 *             return; // No agregar gráfico si no hay datos
 *         }
 *         
 *         $dataRow = 4; // Fila donde comienzan los datos
 *         $lastRow = $dataRow + count($salesData['departments']) - 1;
 *         
 *         // Gráfico de pastel - Distribución de ventas por departamento
 *         $pieChart = new PieChart();
 *         $pieChart->setTitle('Distribución de Ventas por Departamento');
 *         
 *         $categories = new Reference($sheet, 'A' . $dataRow, 'A' . $lastRow);
 *         $values = new Reference($sheet, 'C' . $dataRow, 'C' . $lastRow);
 *         
 *         $pieChart->addSeries(new DataSeries($categories, null, null, $values));
 *         
 *         $sheet->addChart($pieChart);
 *         
 *         // Posición del gráfico
 *         $pieChart->setTopLeftCell('F' . $dataRow);
 *     }
 *     
 *     /**
 *      * Generar nombre de archivo con timestamp
 *      */
 *     private function generateFilename($salesData)
 *     {
 *         $date = date('Y-m-d_His');
 *         $sanitizedName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $salesData['storeName']);
 *         return $this->generateCacheKey(
 *             $salesData['storeId'],
 *             ['startDate' => $salesData['startDate'], 'endDate' => $salesData['endDate']]
 *         ) . '_' . $date . '.xlsx';
 *     }
 *     
 *     /**
 *      * Servir archivo al usuario
 *      */
 *     private function serveFile($filepath)
 *     {
 *         if (!file_exists($filepath)) {
 *             throw new \Exception('Archivo no encontrado');
 *         }
 *         
 *         $filename = basename($filepath);
 *         
 *         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
 *         header('Content-Disposition: attachment; filename="' . addslashes($filename) . '"');
 *         header('Content-Length: ' . filesize($filepath));
 *         header('Cache-Control: public, max-age=3600');
 *         
 *         readfile($filepath);
 *         exit;
 *     }
 *     
 *     /**
 *      * Generar nombre de archivo con timestamp
 *      */
 *     private function generateFilename($salesData)
 *     {
 *         $date = date('Y-m-d_His');
 *         $sanitizedName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $salesData['storeName']);
 *         return $this->generateCacheKey(
 *             $salesData['storeId'],
 *             ['startDate' => $salesData['startDate'], 'endDate' => $salesData['endDate']]
 *         ) . '_' . $date . '.xlsx';
 *     }
 *     
 *     /**
 *      * Servir archivo al usuario
 *      */
 *     private function serveFile($filepath)
 *     {
 *         if (!file_exists($filepath)) {
 *             throw new \Exception('Archivo no encontrado');
 *         }
 *         
 *         $filename = basename($filepath);
 *         
 *         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
 *         header('Content-Disposition: attachment; filename="' . addslashes($filename) . '"');
 *         header('Content-Length: ' . filesize($filepath));
 *         header('Cache-Control: public, max-age=3600');
 *         
 *         readfile($filepath);
 *         exit;
 *     }
 * 
 */

?>
