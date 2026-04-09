<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Entradas</title>
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
<body class="p-4">

    <header class="flex justify-between items-end border-b-2 border-slate-200 pb-6 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-blue-700 rounded-lg shadow-sm flex items-center justify-center text-white font-bold text-xl tracking-tighter">
                {{ config('app.name', 'DUO') }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 leading-tight">Reporte de Entradas</h1>
                <p class="text-sm text-slate-500 font-medium mt-0.5">Movimientos Registrados</p>
            </div>
        </div>
        <div class="text-right">
            <h2 class="text-3xl font-black text-blue-700 uppercase tracking-widest mb-1">ENTRADAS</h2>
            <div class="inline-block bg-slate-100 px-3 py-1 rounded">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Período de Emisión</p>
                <p class="text-sm font-bold text-slate-800">
                    {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </header>

    <section class="grid grid-cols-2 gap-6 mb-8 w-full">
        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 shadow-sm relative">
            <p class="text-sm text-slate-500 font-semibold mb-1 uppercase tracking-wide">Total Operaciones</p>
            <p class="text-3xl font-bold text-slate-900">{{ $total_operaciones }}</p>
        </div>
        <div class="bg-blue-50 p-5 rounded-xl border border-blue-100 shadow-sm relative">
            <p class="text-sm text-blue-600 font-semibold mb-1 uppercase tracking-wide">Total Monto Entrada</p>
            <p class="text-2xl font-bold text-blue-800 break-words leading-tight">${{ number_format($monto_total, 2) }}</p>
        </div>
    </section>

    <main>
        <div class="rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs">ID</th>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs">Usuario / Tienda</th>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs">Pago / Fecha</th>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($movimientos as $mov)
                        <tr class="hover:bg-slate-50 transition-colors {{ $loop->even ? 'bg-slate-50/50' : '' }}">
                            <td class="px-5 py-3.5 font-bold text-blue-700">#{{ $mov->id_transaction }}</td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-800">{{ $mov->user->name ?? 'N/A' }}</div>
                                <div class="text-xs text-slate-500">{{ $mov->store->store ?? 'Sucursal S/N' }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                    {{ $mov->payment->payment ?? 'No Ind.' }}
                                </span>
                                <div class="text-xs mt-1 text-slate-500">{{ \Carbon\Carbon::parse($mov->transaction_date)->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-right font-bold text-slate-800">${{ number_format($mov->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-500 font-medium">No se encontraron entradas registradas en este período.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    <footer class="mt-12 pt-6 border-t border-slate-200 text-center text-xs text-slate-400 font-medium">
        <p>Este informe fue generado automáticamente por {{ config('app.name', 'Soft_Admin') }}.</p>
        <p class="mt-1">Fecha de generación: {{ now()->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i') }} hrs.</p>
    </footer>

</body>
</html>
