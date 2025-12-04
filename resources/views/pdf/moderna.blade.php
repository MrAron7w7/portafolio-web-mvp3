<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['personal']['name'] ?? 'Portafolio' }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        @page { margin: 0px; }
        body { 
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact;
            margin: 0;
            padding: 0;
        }
        /* Clases utilitarias extraídas del style scoped de Vue */
        .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
    </style>
</head>
<body>

    {{-- LÓGICA PHP PARA FECHAS (Réplica de tu JS) --}}
    @php
        if (!function_exists('formatDate')) {
            function formatDate($date) {
                if (!$date) return '';
                // Asumimos formato YYYY-MM-DD
                $parts = explode('-', $date);
                if(count($parts) < 2) return $date;
                
                $months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                $monthIndex = (int)$parts[1] - 1;
                
                return $months[$monthIndex] . ' ' . $parts[0];
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
                    
                    $output = [];
                    
                    if ($years > 0) {
                        $output[] = $years . ' año' . ($years > 1 ? 's' : '');
                    }
                    
                    // Si hay años, mostramos meses si hay meses restantes.
                    // Si no hay años, mostramos meses.
                    if ($months > 0 || $years == 0) {
                        if ($years > 0 && $months > 0) {
                             $output[] = $months . ' mes' . ($months > 1 ? 'es' : '');
                        } elseif ($years == 0) {
                             $output[] = $months . ' mes' . ($months > 1 ? 'es' : '');
                        }
                    }
                    
                    return implode(' ', $output);
                } catch (Exception $e) {
                    return '';
                }
            }
        }
    @endphp

    <div class="min-h-screen bg-slate-50">
        <header class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-cyan-600 px-8 py-16 text-white">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 h-64 w-64 rounded-full bg-white blur-3xl"></div>
                <div class="absolute bottom-0 left-0 h-48 w-48 rounded-full bg-white blur-3xl"></div>
            </div>

            <div class="relative mx-auto max-w-5xl">
                <div class="flex flex-col items-center text-center md:flex-row md:text-left">
                    
                    @if(!empty($data['personal']['photo']))
                    <div class="mb-6 md:mr-8 md:mb-0">
                        <div class="h-32 w-32 overflow-hidden rounded-full border-4 border-white shadow-xl">
                            <img 
                                src="{{ $data['personal']['photo'] }}" 
                                alt="{{ $data['personal']['name'] }}" 
                                class="h-full w-full object-cover"
                            />
                        </div>
                    </div>
                    @endif

                    <div class="flex-1">
                        <h1 class="mb-2 text-4xl font-bold md:text-5xl">
                            {{ $data['personal']['name'] ?? 'Tu Nombre' }}
                        </h1>
                        <p class="mb-4 text-xl text-blue-100 md:text-2xl">
                            {{ $data['personal']['title'] ?? 'Tu Título Profesional' }}
                        </p>

                        <div class="flex flex-wrap items-center justify-center gap-4 md:justify-start">
                            
                            @if(!empty($data['personal']['email']))
                            <a href="mailto:{{ $data['personal']['email'] }}" class="flex items-center space-x-2 rounded-lg bg-white/10 px-3 py-2 backdrop-blur-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                </svg>
                                <span class="text-sm">{{ $data['personal']['email'] }}</span>
                            </a>
                            @endif

                            @if(!empty($data['personal']['phone']))
                            <a href="tel:{{ $data['personal']['phone'] }}" class="flex items-center space-x-2 rounded-lg bg-white/10 px-3 py-2 backdrop-blur-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                                <span class="text-sm">{{ $data['personal']['phone'] }}</span>
                            </a>
                            @endif

                            @if(!empty($data['personal']['location']))
                            <div class="flex items-center space-x-2 rounded-lg bg-white/10 px-3 py-2 backdrop-blur-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                                </svg>
                                <span class="text-sm">{{ $data['personal']['location'] }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="mt-4 flex items-center justify-center gap-3 md:justify-start">
                            
                            @if(!empty($data['personal']['website']))
                            <a href="{{ $data['personal']['website'] }}" target="_blank" class="rounded-lg bg-white/10 p-2 backdrop-blur-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                    <circle cx="12" cy="12" r="10"/><line x1="2" x2="22" y1="12" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1 4-10z"/>
                                </svg>
                            </a>
                            @endif

                            @if(!empty($data['personal']['linkedin']))
                            <a href="{{ $data['personal']['linkedin'] }}" target="_blank" class="rounded-lg bg-white/10 p-2 backdrop-blur-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/>
                                </svg>
                            </a>
                            @endif

                            @if(!empty($data['personal']['github']))
                            <a href="{{ $data['personal']['github'] }}" target="_blank" class="rounded-lg bg-white/10 p-2 backdrop-blur-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                    <path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-8 py-12">
            
            @if(!empty($data['personal']['summary']))
            <section class="mb-12">
                <div class="rounded-2xl bg-white p-8 shadow-md">
                    <h2 class="mb-4 text-2xl font-bold text-gray-900">
                        Perfil Profesional
                    </h2>
                    <p class="leading-relaxed text-gray-700">
                        {{ $data['personal']['summary'] }}
                    </p>
                </div>
            </section>
            @endif

            <div class="grid gap-8 lg:grid-cols-3">
                
                <div class="lg:col-span-2">
                    
                    @if(!empty($data['experience']))
                    <section class="mb-8">
                        <h2 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3 h-6 w-6 text-blue-600">
                                <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                            </svg>
                            Experiencia Laboral
                        </h2>

                        <div class="space-y-6">
                            @foreach($data['experience'] as $exp)
                            <div class="relative rounded-2xl bg-white p-6 shadow-md">
                                <div class="absolute top-8 left-0 h-full w-1 bg-blue-600"></div>

                                <div class="relative pl-8">
                                    <div class="mb-2 flex flex-wrap items-start justify-between">
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">
                                                {{ $exp['position'] }}
                                            </h3>
                                            <p class="text-lg text-blue-600">
                                                {{ $exp['company'] }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mb-3 flex items-center space-x-4 text-sm text-gray-600">
                                        <span class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1 h-4 w-4">
                                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>
                                            </svg>
                                            {{ formatDate($exp['startDate']) }} - 
                                            {{ ($exp['current'] ?? false) ? 'Actualidad' : formatDate($exp['endDate'] ?? '') }}
                                        </span>
                                        <span class="text-gray-400">•</span>
                                        <span>
                                            {{ calculateDuration($exp['startDate'], $exp['endDate'] ?? null, $exp['current'] ?? false) }}
                                        </span>
                                    </div>

                                    <p class="leading-relaxed text-gray-700">
                                        {{ $exp['description'] }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif

                    @if(!empty($data['projects']))
                    <section class="mb-8">
                        <h2 class="mb-6 text-2xl font-bold text-gray-900">
                            Proyectos Destacados
                        </h2>

                        <div class="grid gap-6 md:grid-cols-2">
                            @foreach($data['projects'] as $project)
                            <div class="rounded-2xl bg-white p-6 shadow-md">
                                <h3 class="mb-2 text-lg font-bold text-gray-900">
                                    {{ $project['name'] }}
                                </h3>
                                <p class="mb-3 text-sm text-gray-600">
                                    {{ $project['description'] }}
                                </p>

                                @if(!empty($project['technologies']))
                                <div class="flex flex-wrap gap-2">
                                    @foreach($project['technologies'] as $tech)
                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">
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
                        <div class="rounded-2xl bg-white p-6 shadow-md">
                            <h2 class="mb-4 text-xl font-bold text-gray-900">
                                Habilidades
                            </h2>

                            @if(!empty($data['skills']['technical']))
                            <div class="mb-4">
                                <h3 class="mb-2 text-sm font-semibold text-gray-700">
                                    Técnicas
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($data['skills']['technical'] as $skill)
                                    <span class="rounded-lg bg-blue-50 px-3 py-1 text-sm text-blue-700">
                                        {{ $skill }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if(!empty($data['skills']['soft']))
                            <div>
                                <h3 class="mb-2 text-sm font-semibold text-gray-700">
                                    Blandas
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($data['skills']['soft'] as $skill)
                                    <span class="rounded-lg bg-gray-50 px-3 py-1 text-sm text-gray-700">
                                        {{ $skill }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </section>
                    @endif

                    @if(!empty($data['education']))
                    <section class="mb-8">
                        <div class="rounded-2xl bg-white p-6 shadow-md">
                            <h2 class="mb-4 text-xl font-bold text-gray-900">
                                Educación
                            </h2>

                            <div class="space-y-4">
                                @foreach($data['education'] as $edu)
                                <div class="border-l-2 border-blue-600 pl-4">
                                    <h3 class="font-semibold text-gray-900">
                                        {{ $edu['degree'] }}
                                    </h3>
                                    <p class="text-sm text-blue-600">
                                        {{ $edu['institution'] }}
                                    </p>
                                    <p class="text-xs text-gray-600">
                                        {{ formatDate($edu['startDate']) }} - 
                                        {{ formatDate($edu['endDate']) }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                    @endif

                    @if(!empty($data['languages']))
                    <section class="mb-8">
                        <div class="rounded-2xl bg-white p-6 shadow-md">
                            <h2 class="mb-4 text-xl font-bold text-gray-900">
                                Idiomas
                            </h2>

                            <div class="space-y-3">
                                @foreach($data['languages'] as $lang)
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">
                                        {{ $lang['name'] }}
                                    </span>
                                    <span class="text-sm text-gray-600">
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

        <footer class="border-t border-gray-200 bg-white py-8 text-center">
            <p class="text-gray-600">
                Generado con PortafolioAI • {{ date('Y') }}
            </p>
        </footer>
    </div>
</body>
</html>