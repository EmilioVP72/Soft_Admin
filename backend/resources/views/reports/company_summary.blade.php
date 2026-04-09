<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo ?? 'Resumen Integral' }}</title>
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
                {{ mb_substr($empresa_nombre ?? 'CORP', 0, 4) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 leading-tight">{{ $empresa_nombre ?? 'Empresa Genérica' }}</h1>
                <p class="text-sm text-slate-500 font-medium mt-0.5">Reporte Consolidado Automático</p>
            </div>
        </div>
        <div class="text-right">
            <h2 class="text-2xl font-black text-blue-700 uppercase tracking-widest mb-1">{{ $titulo ?? 'Resumen' }}</h2>
            <div class="inline-block bg-slate-100 px-3 py-1 rounded">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Período Fiscal</p>
                <p class="text-sm font-bold text-slate-800">
                    {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </header>

    <section class="grid grid-cols-3 gap-6 mb-8">
        <div class="bg-emerald-50 p-5 rounded-xl border border-emerald-200 shadow-sm relative overflow-hidden">
            <p class="text-xs text-emerald-600 font-bold mb-1 uppercase tracking-wide truncate">Total Entradas (Ventas)</p>
            <p class="text-2xl font-black text-emerald-900 truncate">${{ number_format($entradas_total, 2) }}</p>
        </div>
        <div class="bg-rose-50 p-5 rounded-xl border border-rose-200 shadow-sm relative overflow-hidden">
            <p class="text-xs text-rose-600 font-bold mb-1 uppercase tracking-wide truncate">Total Salidas (Proveedores)</p>
            <p class="text-2xl font-black text-rose-900 truncate">${{ number_format($salidas_total, 2) }}</p>
        </div>
        <div class="{{ $balance_global >= 0 ? 'bg-blue-50 border-blue-200' : 'bg-orange-50 border-orange-200' }} p-5 rounded-xl border shadow-sm relative overflow-hidden">
            <p class="text-xs {{ $balance_global >= 0 ? 'text-blue-600' : 'text-orange-600' }} font-bold mb-1 uppercase tracking-wide truncate">Balance Global Bruto</p>
            <p class="text-2xl font-black {{ $balance_global >= 0 ? 'text-blue-900' : 'text-orange-900' }} truncate">${{ number_format($balance_global, 2) }}</p>
        </div>
    </section>

    <main class="grid grid-cols-2 gap-8">
        
        <!-- Tabla Sucursales -->
        <div class="rounded-xl border border-slate-200 shadow-sm w-full overflow-hidden self-start">
            <div class="bg-slate-100 px-4 py-3 border-b border-slate-200">
                <h3 class="font-bold text-slate-700 uppercase tracking-wider text-xs">Ventas Agrupadas por Sucursal</h3>
            </div>
            <table class="w-full text-left text-sm table-fixed">
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($ventas_por_sucursal as $store => $amount)
                        <tr class="hover:bg-slate-50 transition-colors {{ $loop->even ? 'bg-slate-50/50' : '' }}">
                            <td class="px-5 py-3 font-semibold text-slate-700 truncate w-3/5">{{ $store }}</td>
                            <td class="px-5 py-3 text-right font-bold text-slate-800 whitespace-nowrap">${{ number_format($amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-5 py-8 text-center text-slate-500 font-medium">No hay ventas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tabla Departamentos -->
        <div class="rounded-xl border border-slate-200 shadow-sm w-full overflow-hidden self-start">
            <div class="bg-slate-100 px-4 py-3 border-b border-slate-200">
                <h3 class="font-bold text-slate-700 uppercase tracking-wider text-xs">Ventas Agrupadas por Departamento</h3>
            </div>
            <table class="w-full text-left text-sm table-fixed">
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($ventas_por_departamento as $dept => $amount)
                        <tr class="hover:bg-slate-50 transition-colors {{ $loop->even ? 'bg-slate-50/50' : '' }}">
                            <td class="px-5 py-3 font-semibold text-slate-700 truncate w-3/5">{{ $dept }}</td>
                            <td class="px-5 py-3 text-right font-bold text-slate-800 whitespace-nowrap">${{ number_format($amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-5 py-8 text-center text-slate-500 font-medium">No hay ventas departamentales.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </main>

    <footer class="mt-12 pt-6 border-t border-slate-200 text-center text-xs text-slate-400 font-medium">
        <p>Informe Gerencial Confidencial - {{ config('app.name', 'Soft_Admin') }}</p>
        <p class="mt-1">Automáticamente expedido el {{ now()->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i') }} hrs.</p>
    </footer>

</body>
</html>
