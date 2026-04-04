<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        
        @page {
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
                <p class="text-sm text-slate-500 font-medium mt-0.5">Reporte Dinámico</p>
                <p class="text-sm text-slate-500">{{ $empresa_direccion ?? 'Sin dirección' }}</p>
            </div>
        </div>
        <div class="text-right">
            <h2 class="text-3xl font-black text-blue-700 uppercase tracking-widest mb-1">{{ $titulo ?? 'Ventas Generales' }}</h2>
            <div class="inline-block bg-slate-100 px-3 py-1 rounded">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Período del Reporte</p>
                <p class="text-sm font-bold text-slate-800">
                    {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </header>

    <section class="grid grid-cols-3 gap-6 mb-8">
        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10 text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-sm text-slate-500 font-semibold mb-1 uppercase tracking-wide">Total Recaudado</p>
            <p class="text-3xl font-bold text-slate-900">${{ number_format($total_recaudado, 2) }}</p>
        </div>
        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10 text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <p class="text-sm text-slate-500 font-semibold mb-1 uppercase tracking-wide">Operaciones</p>
            <p class="text-3xl font-bold text-slate-900">{{ $operaciones_count }}</p>
        </div>
        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10 text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <p class="text-sm text-slate-500 font-semibold mb-1 uppercase tracking-wide">Ticket Promedio</p>
            <p class="text-3xl font-bold text-slate-900">${{ number_format($ticket_promedio, 2) }}</p>
        </div>
    </section>

    <main>
        <div class="rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-100 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs">Fecha y Hora</th>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs">ID Venta</th>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs">Sucursal</th>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs">Método de Pago</th>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs text-right">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($ventas as $venta)
                        <tr class="hover:bg-slate-50 transition-colors {{ $loop->even ? 'bg-slate-50/50' : '' }}">
                            <td class="px-5 py-3.5 text-slate-500">{{ \Carbon\Carbon::parse($venta->transaction_date)->format('d M Y, H:i') }}</td>
                            <td class="px-5 py-3.5 font-semibold text-blue-700">#{{ $venta->id_transaction }}</td>
                            <td class="px-5 py-3.5 font-medium text-slate-700">{{ $venta->store->store ?? 'N/A' }}</td>
                            <td class="px-5 py-3.5">
                                @php
                                    $metodo = $venta->payment->payment_type ?? 'Desconocido';
                                    $metodoLower = strtolower($metodo);
                                @endphp
                                @if (str_contains($metodoLower, 'efectivo'))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
                                        {{ $metodo }}
                                    </span>
                                @elseif (str_contains($metodoLower, 'tarjeta') || str_contains($metodoLower, 'card'))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200">
                                        {{ $metodo }}
                                    </span>
                                @elseif (str_contains($metodoLower, 'transfer'))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-700 border border-purple-200">
                                        {{ $metodo }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $metodo }}
                                    </span>
                                @endif
                                
                            </td>
                            <td class="px-5 py-3.5 text-right font-bold text-slate-800">${{ number_format($venta->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-500 font-medium">No se encontraron ventas en este período.</td>
                        </tr>
                    @endforelse

                </tbody>
                <tfoot>
                    <tr class="bg-slate-800 text-white">
                        <td colspan="4" class="px-5 py-4 text-right font-bold tracking-wide">TOTAL DEL REPORTE:</td>
                        <td class="px-5 py-4 text-right font-black text-lg">${{ number_format($total_recaudado, 2) }}</td>
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
