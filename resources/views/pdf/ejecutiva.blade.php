<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['personal']['name'] ?? 'CV Ejecutivo' }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        @page { margin: 0px; }
        body { 
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact;
            margin: 0;
            padding: 0;
            background-color: #f9fafb; /* bg-gray-50 */
        }
        .transition { transition: all 0.3s ease; }
    </style>
</head>
<body>

    {{-- LÓGICA PHP (Helpers) --}}
    @php
        if (!function_exists('formatDate')) {
            function formatDate($date) {
                if (!$date) return '';
                $parts = explode('-', $date);
                if(count($parts) < 2) return $date;
                $months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                $monthIndex = (int)$parts[1] - 1;
                return ($months[$monthIndex] ?? '') . ' ' . $parts[0];
            }
        }

        if (!function_exists('calculateDuration')) {
            function calculateDuration($start, $end, $current) {
                if (!$start) return '';
                try {
                    $startDate = new DateTime($start);
                    $endDate = $current ? new DateTime() : ($end ? new DateTime($end) : new DateTime());
                    
                    $interval = $startDate->diff($endDate);
                    $years = $interval->y;
                    $months = $interval->m;
                    
                    if ($years > 0 && $months > 0) {
                        return $years . 'a ' . $months . 'm';
                    } elseif ($years > 0) {
                        return $years . 'a';
                    } else {
                        return $months . 'm';
                    }
                } catch (Exception $e) {
                    return '';
                }
            }
        }
    @endphp

    <div class="min-h-screen bg-gray-50">
        <header class="bg-gray-900 text-white">
            <div class="mx-auto max-w-6xl px-8 py-12">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h1 class="mb-2 text-4xl font-bold">
                            {{ $data['personal']['name'] ?? 'Tu Nombre' }}
                        </h1>
                        <p class="mb-6 text-xl text-gray-300">
                            {{ $data['personal']['title'] ?? 'Director Ejecutivo' }}
                        </p>

                        @if(!empty($data['personal']['summary']))
                        <p class="max-w-2xl leading-relaxed text-gray-400">
                            {{ $data['personal']['summary'] }}
                        </p>
                        @endif
                    </div>

                    <div class="text-right">
                        <div class="space-y-2 text-sm">
                            @if(!empty($data['personal']['email']))
                            <div class="flex items-center justify-end">
                                <span class="text-gray-300">{{ $data['personal']['email'] }}</span>
                            </div>
                            @endif

                            @if(!empty($data['personal']['phone']))
                            <div class="flex items-center justify-end">
                                <span class="text-gray-300">{{ $data['personal']['phone'] }}</span>
                            </div>
                            @endif

                            @if(!empty($data['personal']['location']))
                            <div class="flex items-center justify-end">
                                <span class="text-gray-300">{{ $data['personal']['location'] }}</span>
                            </div>
                            @endif

                            @if(!empty($data['personal']['linkedin']))
                            <div class="flex items-center justify-end">
                                <span class="text-gray-300">{{ $data['personal']['linkedin'] }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-8 py-12">
            <div class="grid gap-8 lg:grid-cols-3">
                
                <div class="lg:col-span-2">
                    
                    @if(!empty($data['experience']))
                    <section class="mb-8">
                        <div class="mb-6 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3 h-6 w-6 text-gray-700">
                                <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                            </svg>
                            <h2 class="text-2xl font-bold text-gray-900">
                                Experiencia Ejecutiva
                            </h2>
                        </div>

                        <div class="space-y-6">
                            @foreach($data['experience'] as $exp)
                            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                                <div class="mb-3 flex items-start justify-between">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">
                                            {{ $exp['position'] }}
                                        </h3>
                                        <p class="text-lg font-semibold text-blue-600">
                                            {{ $exp['company'] }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-sm font-medium text-gray-700">
                                            {{ formatDate($exp['startDate']) }} – 
                                            {{ ($exp['current'] ?? false) ? 'Presente' : formatDate($exp['endDate'] ?? '') }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ calculateDuration($exp['startDate'], $exp['endDate'] ?? null, $exp['current'] ?? false) }}
                                        </span>
                                    </div>
                                </div>

                                <p class="mb-4 leading-relaxed text-gray-700">
                                    {{ $exp['description'] }}
                                </p>

                                @if(!empty($exp['achievements']))
                                <div class="rounded-lg bg-gray-50 p-4">
                                    <h4 class="mb-2 flex items-center font-semibold text-gray-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4 text-green-600">
                                            <circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>
                                        </svg>
                                        Logros Clave
                                    </h4>
                                    <ul class="space-y-1">
                                        @foreach($exp['achievements'] as $achievement)
                                        <li class="flex items-start text-sm text-gray-700">
                                            <span class="mr-2 text-green-500">•</span>
                                            {{ $achievement }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif

                    @if(!empty($data['projects']))
                    <section class="mb-8">
                        <div class="mb-6 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3 h-6 w-6 text-gray-700">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
                            </svg>
                            <h2 class="text-2xl font-bold text-gray-900">
                                Proyectos Estratégicos
                            </h2>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            @foreach($data['projects'] as $project)
                            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                                <h3 class="mb-2 font-bold text-gray-900">
                                    {{ $project['name'] }}
                                </h3>
                                <p class="mb-3 text-sm text-gray-700">
                                    {{ $project['description'] }}
                                </p>
                                @if(!empty($project['technologies']))
                                <div class="flex flex-wrap gap-1">
                                    @foreach($project['technologies'] as $tech)
                                    <span class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-700">
                                        {{ $tech }}
                                    </span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif
                </div>

                <div>
                    @if(!empty($data['skills']['technical']) || !empty($data['skills']['soft']))
                    <section class="mb-8">
                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-5 w-5 text-gray-700">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <h2 class="text-xl font-bold text-gray-900">
                                    Competencias
                                </h2>
                            </div>

                            @if(!empty($data['skills']['technical']))
                            <div class="mb-4">
                                <h3 class="mb-2 font-semibold text-gray-700">
                                    Liderazgo Técnico
                                </h3>
                                <div class="space-y-2">
                                    @foreach($data['skills']['technical'] as $skill)
                                    <div class="flex items-center">
                                        <div class="mr-3 h-2 w-2 rounded-full bg-blue-600"></div>
                                        <span class="text-sm text-gray-700">{{ $skill }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if(!empty($data['skills']['soft']))
                            <div>
                                <h3 class="mb-2 font-semibold text-gray-700">
                                    Habilidades Directivas
                                </h3>
                                <div class="space-y-2">
                                    @foreach($data['skills']['soft'] as $skill)
                                    <div class="flex items-center">
                                        <div class="mr-3 h-2 w-2 rounded-full bg-green-600"></div>
                                        <span class="text-sm text-gray-700">{{ $skill }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </section>
                    @endif

                    @if(!empty($data['education']))
                    <section class="mb-8">
                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-5 w-5 text-gray-700">
                                    <circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>
                                </svg>
                                <h2 class="text-xl font-bold text-gray-900">
                                    Formación Académica
                                </h2>
                            </div>

                            <div class="space-y-4">
                                @foreach($data['education'] as $edu)
                                <div class="border-l-2 border-blue-600 pl-4">
                                    <h3 class="font-semibold text-gray-900">
                                        {{ $edu['degree'] }}
                                    </h3>
                                    <p class="text-sm text-blue-600">
                                        {{ $edu['institution'] }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ formatDate($edu['startDate']) }} - 
                                        {{ formatDate($edu['endDate']) }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                    @endif

                    @if(!empty($data['certifications']))
                    <section class="mb-8">
                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-xl font-bold text-gray-900">
                                Certificaciones
                            </h2>
                            <div class="space-y-3">
                                @foreach($data['certifications'] as $cert)
                                <div class="flex items-start">
                                    <div class="mr-3 rounded bg-blue-100 p-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-blue-600">
                                            <circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900">
                                            {{ $cert['name'] }}
                                        </h3>
                                        <p class="text-xs text-gray-500">
                                            {{ $cert['issuer'] }} • {{ $cert['date'] }}
                                        </p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                    @endif

                    @if(!empty($data['languages']))
                    <section>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-xl font-bold text-gray-900">
                                Idiomas
                            </h2>
                            <div class="space-y-2">
                                @foreach($data['languages'] as $lang)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-700">{{ $lang['name'] }}</span>
                                    <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-600">
                                        {{ $lang['level'] }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                    @endif

                </div>
            </div>
        </main>

        <footer class="border-t border-gray-200 bg-white py-6">
            <div class="mx-auto max-w-6xl px-8 text-center">
                <p class="text-sm text-gray-600">
                    {{ $data['personal']['name'] ?? '' }} • Ejecutivo Profesional • {{ date('Y') }}
                </p>
            </div>
        </footer>
    </div>
</body>
</html>