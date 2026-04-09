<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Financiero</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
        <div style="background-color: #1d4ed8; color: white; padding: 20px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px;">Resumen Integral de Operaciones</h1>
            <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.9;">Corte {{ ucfirst($period) }}</p>
        </div>
        <div style="padding: 30px;">
            <p>Hola Ejecutivo,</p>
            <p>Adjunto a este correo encontrarás el informe financiero generado automáticamente correspondiente al periodo del <strong>{{ $startDate->format('d/m/Y') }}</strong> al <strong>{{ $endDate->format('d/m/Y') }}</strong>.</p>
            <p>Este informe engloba:</p>
            <ul>
                <li>Entradas brutas por ventas.</li>
                <li>Salidas absolutas por pagos emitidos.</li>
                <li>Desglose métrico por Sucursal.</li>
                <li>Desglose productivo por Departamento.</li>
            </ul>
            <p>Favor de revisar el archivo PDF adjunto de manera estrictamente confidencial.</p>
            <br>
            <p style="font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 10px;">
                Este correo fue enviado por el sistema Soft Admin y no requiere ser respondido.
            </p>
        </div>
    </div>
</body>
</html>
