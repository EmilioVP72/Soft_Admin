<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo ?? 'Promociones' }}</title>
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
                CALC
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 leading-tight">{{ $empresa_nombre ?? 'Calculadora' }}</h1>
                <p class="text-sm text-slate-500 font-medium mt-0.5">Módulo de Retornos</p>
            </div>
        </div>
        <div class="text-right">
            <h2 class="text-2xl font-black text-blue-700 uppercase tracking-widest mb-1">{{ $titulo ?? 'Promociones' }}</h2>
            <div class="inline-block bg-slate-100 px-3 py-1 rounded">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Fecha de Emisión</p>
                <p class="text-sm font-bold text-slate-800">{{ now()->format('d/m/Y') }}</p>
            </div>
        </div>
    </header>

    <main>
        <div class="rounded-xl border border-slate-200 shadow-sm w-full overflow-hidden">
            <table class="w-full text-left text-sm table-fixed">
                <thead class="bg-slate-100 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs">Fecha</th>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs text-right">Total Ventas</th>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs text-right">Acumulado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($items as $item)
                        <tr class="hover:bg-slate-50 transition-colors {{ $loop->even ? 'bg-slate-50/50' : '' }}">
                            <td class="px-5 py-3 font-semibold text-slate-700 whitespace-nowrap">{{ $item['date'] ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-right font-medium text-slate-600 whitespace-nowrap">${{ number_format((float)($item['totaly_sales'] ?? 0), 2) }}</td>
                            <td class="px-5 py-3 text-right font-bold text-slate-800 whitespace-nowrap">${{ number_format((float)($item['acumulated_sales'] ?? 0), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-8 text-center text-slate-500 font-medium">No se suministraron datos desde la calculadora.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($items) > 0)
                <tfoot>
                    <tr class="bg-slate-800 text-white">
                        <td colspan="2" class="px-5 py-3 text-right font-bold tracking-wide">TOTAL ACUMULADO FINAL:</td>
                        <td class="px-5 py-3 text-right font-black whitespace-nowrap">${{ number_format((float)(end($items)['acumulated_sales'] ?? 0), 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </main>

    <footer class="mt-12 pt-6 border-t border-slate-200 text-center text-xs text-slate-400 font-medium">
        <p>Este informe fue generado automáticamente por el sistema de Gestión Integral.</p>
        <p class="mt-1">Fecha de generación: {{ now()->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i') }} hrs.</p>
    </footer>

</body>
</html>
