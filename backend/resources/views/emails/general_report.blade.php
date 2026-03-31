<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte {{ ucfirst($reportData['period']) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #2563eb;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .content {
            padding: 30px;
        }
        h2 {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
            color: #1f2937;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        table th {
            background-color: #f9fafb;
            color: #4b5563;
        }
        .summary-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 20px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .summary-item {
            flex: 1;
            min-width: 150px;
            margin: 10px;
            text-align: center;
        }
        .summary-item span {
            display: block;
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        .summary-item strong {
            display: block;
            font-size: 24px;
            color: #1d4ed8;
        }
        .text-green { color: #16a34a !important; }
        .text-red { color: #dc2626 !important; }
        .text-blue { color: #2563eb !important; }
    </style>
</head>
<body>
    <div class="container">
        @php
            $periodLabels = [
                'weekly' => 'Semanal',
                'monthly' => 'Mensual',
                'yearly' => 'Anual'
            ];
            $periodName = $periodLabels[$reportData['period']] ?? 'General';
        @endphp

        <div class="header">
            <h1>Reporte General {{ $periodName }}</h1>
            <p>Del {{ $reportData['start_date']->format('d/m/Y') }} al {{ $reportData['end_date']->format('d/m/Y') }}</p>
        </div>

        <div class="content">
            <h2>Resumen Financiero</h2>
            <div class="summary-box">
                <div class="summary-item">
                    <span>Entradas Totales</span>
                    <strong class="text-green">${{ number_format($reportData['inputs'], 2) }}</strong>
                </div>
                <div class="summary-item">
                    <span>Ventas Generales</span>
                    <strong class="text-blue">${{ number_format($reportData['general_sales'], 2) }}</strong>
                </div>
                <div class="summary-item">
                    <span>Salidas Totales</span>
                    <strong class="text-red">${{ number_format($reportData['outputs'], 2) }}</strong>
                </div>
                <div class="summary-item">
                    <span>Balance / Flujo Neto</span>
                    <strong>${{ number_format($reportData['net_profit'], 2) }}</strong>
                </div>
            </div>

            <h2>Ventas por Departamento</h2>
            @if(count($reportData['sales_by_department']) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Departamento</th>
                            <th>Artículos Vendidos</th>
                            <th>Total de Ventas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData['sales_by_department'] as $dept)
                            <tr>
                                <td>{{ $dept->department }}</td>
                                <td>{{ number_format($dept->total_quantity, 2) }}</td>
                                <td>${{ number_format($dept->total_sales, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No se registraron ventas por departamento en este periodo.</p>
            @endif

            <p style="text-align: center; color: #9ca3af; font-size: 12px; margin-top: 40px;">
                Este correo fue generado automáticamente por el sistema Soft Admin.
            </p>
        </div>
    </div>
</body>
</html>
