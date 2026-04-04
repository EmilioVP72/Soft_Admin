<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Sucursales</title>
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
                <h1 class="text-2xl font-bold text-slate-900 leading-tight">Lista de Tiendas</h1>
                <p class="text-sm text-slate-500 font-medium mt-0.5">Directorio Corporativo</p>
            </div>
        </div>
        <div class="text-right">
            <h2 class="text-3xl font-black text-blue-700 uppercase tracking-widest mb-1">Reporte de Sucursales</h2>
            <div class="inline-block bg-slate-100 px-3 py-1 rounded">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Fecha de Emisión</p>
                <p class="text-sm font-bold text-slate-800">
                    {{ now()->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </header>

    <section class="grid grid-cols-1 gap-6 mb-8 w-1/3">
        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
            <p class="text-sm text-slate-500 font-semibold mb-1 uppercase tracking-wide">Total Sucursales Registradas</p>
            <p class="text-3xl font-bold text-slate-900">{{ $total_sucursales }}</p>
        </div>
    </section>

    <main>
        <div class="rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-100 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs">Identificador</th>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs">Dirección</th>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs">Referencia</th>
                        <th scope="col" class="px-5 py-4 font-bold uppercase tracking-wider text-xs">Localidad</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($sucursales as $sucursal)
                        <tr class="hover:bg-slate-50 transition-colors {{ $loop->even ? 'bg-slate-50/50' : '' }}">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-800">{{ $sucursal->name }}</div>
                                <div class="text-xs text-slate-500">ID: {{ $sucursal->id_store }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-medium text-slate-700">{{ $sucursal->street }} {{ $sucursal->exterior_number }} @if($sucursal->interior_number) - Int. {{ $sucursal->interior_number }} @endif</div>
                                <div class="text-xs text-slate-500">Col. {{ $sucursal->colony }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-medium text-slate-700 text-wrap">{{ $sucursal->reference ?? 'N/A' }}</td>
                            <td class="px-5 py-3.5 font-medium text-slate-700">{{ $sucursal->locality->locality ?? 'Sin Asignar' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-500 font-medium">No se encontraron sucursales en la base de datos.</td>
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
