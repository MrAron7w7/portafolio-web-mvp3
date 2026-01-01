<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Actividad {{ $year }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: sans-serif; }
    </style>
</head>
<body class="p-8 bg-white">
    <div class="mb-8 border-b pb-4 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Reporte de Actividad</h1>
            <p class="text-gray-500 text-sm">Generado el: {{ $generatedAt }}</p>
        </div>
        <div class="text-right">
            <h2 class="text-xl font-bold text-[#005aeb]">{{ $year }}</h2>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-2 gap-6 mb-8">
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-sm text-gray-500 uppercase">Usuarios Totales</p>
            <p class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-sm text-gray-500 uppercase">Portafolios Totales</p>
            <p class="text-2xl font-bold text-gray-800">{{ $totalPortfolios }}</p>
        </div>
    </div>

    <!-- Monthly Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 font-semibold text-gray-600">Mes</th>
                    <th class="px-6 py-3 font-semibold text-gray-600">Usuarios Registrados</th>
                    <th class="px-6 py-3 font-semibold text-gray-600">Portafolios Creados</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($data as $row)
                <tr>
                    <td class="px-6 py-3 text-gray-900 font-medium">{{ $row['month'] }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $row['users'] }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $row['portfolios'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-8 text-center text-xs text-gray-400">
        <p>Reporte generado automáticamente por el sistema de administración.</p>
    </div>
</body>
</html>
