<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo ?? 'Ventas Generales' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        
        @page {
            size: A4 portrait;
            margin: 10mm; 
        }
        body {
            font-family: 'Inter', sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            background: white;
            color: #1e293b; 
        }
    </style>
</head>
<body class="p-8 max-w-5xl mx-auto">

    <header class="flex justify-between items-end border-b-2 border-slate-200 pb-6 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-blue-700 rounded-lg shadow-sm flex items-center justify-center text-white font-bold text-xl tracking-tighter">
                {{ config('app.name', 'DUO') }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 leading-tight">{{ $empresa_nombre ?? 'Empresa Genérica' }}</h1>
                <p class="text-sm text-slate-500 font-medium mt-0.5">{{ $empresa_direccion ?? 'Resumen Consolidado' }}</p>
            </div>
        </div>
        <div class="text-right">
            <h2 class="text-2xl font-black text-blue-700 uppercase tracking-widest mb-1">{{ $titulo ?? 'Ventas Generales' }}</h2>
            <div class="inline-block bg-slate-100 px-3 py-1 rounded">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Período del Reporte</p>
                <p class="text-sm font-bold text-slate-800">
                    {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </header>

    <section class="grid grid-cols-2 gap-6 mb-8 w-2/3">
        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
            <p class="text-xs text-slate-500 font-semibold mb-1 uppercase tracking-wide truncate">Total Recaudado</p>
            <p class="text-2xl font-bold text-slate-900 truncate">${{ number_format($total_recaudado, 2) }}</p>
        </div>
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
            <p class="text-xs text-slate-500 font-semibold mb-1 uppercase tracking-wide truncate">Cantidad de Artículos</p>
            <p class="text-2xl font-bold text-slate-900 truncate">{{ $operaciones_count }}</p>
        </div>
    </section>

    <main>
        <div class="rounded-xl border border-slate-200 shadow-sm w-full overflow-hidden">
            <table class="w-full text-left text-sm table-fixed">
                <thead class="bg-slate-100 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs">Departamento</th>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs text-right">Cantidad Vendida</th>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs text-right">Ingresos Totales</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($departamentos as $dept)
                        <tr class="hover:bg-slate-50 transition-colors {{ $loop->even ? 'bg-slate-50/50' : '' }}">
                            <td class="px-5 py-3 font-semibold text-slate-700 whitespace-nowrap">{{ $dept['name'] }}</td>
                            <td class="px-5 py-3 text-right font-medium text-slate-600 whitespace-nowrap">{{ $dept['quantity'] }}</td>
                            <td class="px-5 py-3 text-right font-bold text-slate-800 whitespace-nowrap">${{ number_format($dept['total'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-8 text-center text-slate-500 font-medium">No se encontraron ventas en este período.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-slate-800 text-white">
                        <td class="px-5 py-3 text-right font-bold tracking-wide">TOTAL GENERAL:</td>
                        <td class="px-5 py-3 text-right font-bold whitespace-nowrap">{{ $operaciones_count }}</td>
                        <td class="px-5 py-3 text-right font-black whitespace-nowrap">${{ number_format($total_recaudado, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </main>

    <footer class="mt-12 pt-6 border-t border-slate-200 text-center text-xs text-slate-400 font-medium">
        <p>Este informe fue generado automáticamente por {{ config('app.name', 'Soft_Admin') }}.</p>
        <p class="mt-1">Fecha de generación: {{ now()->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i') }} hrs.</p>
    </footer>

</body>
</html>
