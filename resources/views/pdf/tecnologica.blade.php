<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['personal']['name'] ?? 'CV Tecnológico' }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        @page { margin: 0px; }
        body { 
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; /* Fuente Monospace para look tech */
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact;
            margin: 0;
            padding: 0;
            background-color: #111827; /* gray-900 fallback */
        }
        
        /* Patrón de Grid Tecnológico */
        .bg-grid-white {
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 50px 50px;
        }
        
        .backdrop-blur-sm {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="text-white">

    {{-- LÓGICA PHP (Helper para fechas) --}}
    @php
        if (!function_exists('formatDate')) {
            function formatDate($date) {
                if (!$date) return '';
                $parts = explode('-', $date);
                if(count($parts) < 2) return $date;
                return $parts[1] . '/' . $parts[0];
            }
        }
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-gray-900 to-blue-900 text-white">
        
        <header class="relative overflow-hidden px-8 py-16">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 h-64 w-64 rounded-full bg-blue-500 blur-3xl"></div>
                <div class="absolute right-0 bottom-0 h-64 w-64 rounded-full bg-cyan-500 blur-3xl"></div>
            </div>

            <div class="bg-grid-white absolute inset-0 opacity-20"></div>

            <div class="relative mx-auto max-w-6xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="mb-3 text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400">
                            {{ $data['personal']['name'] ?? 'Tu Nombre' }}
                        </h1>
                        <p class="mb-6 text-2xl text-gray-300">
                            {{ $data['personal']['title'] ?? 'Desarrollador de Software' }}
                        </p>

                        @if(!empty($data['skills']['technical']))
                        <div class="flex flex-wrap gap-2">
                            @foreach(array_slice($data['skills']['technical'], 0, 5) as $tech)
                            <span class="rounded-full border border-blue-400/30 bg-blue-400/10 px-4 py-2 text-sm backdrop-blur-sm">
                                {{ $tech }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div class="text-right">
                        <div class="space-y-2 text-sm text-gray-300">
                            @if(!empty($data['personal']['email']))
                            <div class="flex items-center justify-end">
                                <span>{{ $data['personal']['email'] }}</span>
                            </div>
                            @endif

                            @if(!empty($data['personal']['phone']))
                            <div class="flex items-center justify-end">
                                <span>{{ $data['personal']['phone'] }}</span>
                            </div>
                            @endif

                            @if(!empty($data['personal']['location']))
                            <div class="flex items-center justify-end">
                                <span>{{ $data['personal']['location'] }}</span>
                            </div>
                            @endif

                            <div class="mt-3 flex justify-end space-x-3">
                                @if(!empty($data['personal']['github']))
                                <a href="{{ $data['personal']['github'] }}" target="_blank" class="rounded-lg bg-white/10 p-2 backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                        <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
                                    </svg>
                                </a>
                                @endif

                                @if(!empty($data['personal']['linkedin']))
                                <a href="{{ $data['personal']['linkedin'] }}" target="_blank" class="rounded-lg bg-white/10 p-2 backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                        <polyline points="4 17 10 11 4 5"/><line x1="12" x2="20" y1="19" y2="19"/>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="relative mx-auto max-w-6xl px-8 py-12">
            
            @if(!empty($data['personal']['summary']))
            <section class="mb-12">
                <div class="rounded-2xl border border-blue-400/20 bg-blue-400/5 p-8 backdrop-blur-sm">
                    <h2 class="mb-4 flex items-center text-2xl font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3 h-6 w-6 text-cyan-400">
                            <polyline points="4 17 10 11 4 5"/><line x1="12" x2="20" y1="19" y2="19"/>
                        </svg>
                        Perfil Técnico
                    </h2>
                    <p class="leading-relaxed text-gray-300">
                        {{ $data['personal']['summary'] }}
                    </p>
                </div>
            </section>
            @endif

            <div class="grid gap-8 lg:grid-cols-3">
                
                <div class="lg:col-span-2">
                    
                    @if(!empty($data['experience']))
                    <section class="mb-8">
                        <h2 class="mb-6 flex items-center text-2xl font-bold">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3 h-6 w-6 text-cyan-400">
                                <rect width="20" height="8" x="2" y="2" rx="2" ry="2"/><rect width="20" height="8" x="2" y="14" rx="2" ry="2"/><line x1="6" x2="6.01" y1="6" y2="6"/><line x1="6" x2="6.01" y1="18" y2="18"/>
                            </svg>
                            Experiencia Técnica
                        </h2>

                        <div class="space-y-6">
                            @foreach($data['experience'] as $exp)
                            <div class="rounded-2xl border border-blue-400/20 bg-blue-400/5 p-6 backdrop-blur-sm">
                                <div class="mb-4 flex items-start justify-between">
                                    <div>
                                        <h3 class="text-xl font-bold">
                                            {{ $exp['position'] }}
                                        </h3>
                                        <p class="text-cyan-400">
                                            {{ $exp['company'] }}
                                        </p>
                                    </div>
                                    <span class="rounded-full bg-cyan-400/20 px-4 py-1 text-sm text-cyan-300">
                                        {{ formatDate($exp['startDate']) }} – 
                                        {{ ($exp['current'] ?? false) ? 'Presente' : formatDate($exp['endDate'] ?? '') }}
                                    </span>
                                </div>

                                <p class="mb-4 leading-relaxed text-gray-300">
                                    {{ $exp['description'] }}
                                </p>

                                @if(!empty($exp['technologies']))
                                <div class="flex flex-wrap gap-2">
                                    @foreach($exp['technologies'] as $tech)
                                    <span class="rounded-full border border-cyan-400/30 bg-cyan-400/10 px-3 py-1 text-xs">
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

                    @if(!empty($data['projects']))
                    <section class="mb-8">
                        <h2 class="mb-6 flex items-center text-2xl font-bold">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3 h-6 w-6 text-cyan-400">
                                <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
                            </svg>
                            Proyectos Técnicos
                        </h2>

                        <div class="grid gap-4 md:grid-cols-2">
                            @foreach($data['projects'] as $project)
                            <div class="rounded-xl border border-blue-400/20 bg-blue-400/5 p-6 backdrop-blur-sm">
                                <h3 class="mb-2 font-bold">
                                    {{ $project['name'] }}
                                </h3>
                                <p class="mb-3 text-sm text-gray-400">
                                    {{ $project['description'] }}
                                </p>

                                @if(!empty($project['technologies']))
                                <div class="flex flex-wrap gap-1">
                                    @foreach($project['technologies'] as $tech)
                                    <span class="rounded-full bg-cyan-400/20 px-2 py-1 text-xs text-cyan-300">
                                        {{ $tech }}
                                    </span>
                                    @endforeach
                                </div>
                                @endif

                                @if(!empty($project['url']))
                                <a href="{{ $project['url'] }}" target="_blank" class="mt-3 inline-block text-sm text-cyan-400 hover:text-cyan-300">
                                    Ver código →
                                </a>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif
                </div>

                <div>
                    @if(!empty($data['skills']['technical']))
                    <section class="mb-8">
                        <div class="rounded-2xl border border-blue-400/20 bg-blue-400/5 p-6 backdrop-blur-sm">
                            <h2 class="mb-4 flex items-center text-xl font-bold">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-5 w-5 text-cyan-400">
                                    <rect width="16" height="16" x="4" y="4" rx="2"/><rect width="6" height="6" x="9" y="9" rx="1"/><path d="M15 2v2"/><path d="M15 20v2"/><path d="M2 15h2"/><path d="M2 9h2"/><path d="M20 15h2"/><path d="M20 9h2"/><path d="M9 2v2"/><path d="M9 20v2"/>
                                </svg>
                                Tech Stack
                            </h2>

                            <div class="space-y-4">
                                @foreach($data['skills']['technical'] as $skill)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3 h-4 w-4 text-cyan-400">
                                            <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
                                        </svg>
                                        <span class="text-sm">{{ $skill }}</span>
                                    </div>
                                    <div class="h-1 w-16 rounded-full bg-gray-700">
                                        <div class="h-full rounded-full bg-cyan-400" style="width: 85%"></div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                    @endif

                    @if(!empty($data['education']))
                    <section class="mb-8">
                        <div class="rounded-2xl border border-blue-400/20 bg-blue-400/5 p-6 backdrop-blur-sm">
                            <h2 class="mb-4 text-xl font-bold">Educación</h2>
                            <div class="space-y-4">
                                @foreach($data['education'] as $edu)
                                <div class="border-l-2 border-cyan-400 pl-4">
                                    <h3 class="font-semibold">
                                        {{ $edu['degree'] }}
                                    </h3>
                                    <p class="text-sm text-cyan-400">
                                        {{ $edu['institution'] }}
                                    </p>
                                    <p class="text-xs text-gray-400">
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
                        <div class="rounded-2xl border border-blue-400/20 bg-blue-400/5 p-6 backdrop-blur-sm">
                            <h2 class="mb-4 text-xl font-bold">
                                Certificaciones
                            </h2>
                            <div class="space-y-3">
                                @foreach($data['certifications'] as $cert)
                                <div class="flex items-start">
                                    <div class="mr-3 rounded bg-cyan-400/20 p-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-cyan-400">
                                            <path d="M17.5 19c0-3.037-2.463-5.5-5.5-5.5S6.5 15.963 6.5 19"/><path d="M4 19c-2.21 0-4-1.79-4-4 0-2.023 1.503-3.69 3.444-3.957A5.5 5.5 0 0 1 13.5 6.5c2.618 0 4.808 1.83 5.373 4.318A4.002 4.002 0 0 1 20 19"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold">
                                            {{ $cert['name'] }}
                                        </h3>
                                        <p class="text-xs text-gray-400">
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
                    <section class="mb-8">
                        <div class="rounded-2xl border border-blue-400/20 bg-blue-400/5 p-6 backdrop-blur-sm">
                            <h2 class="mb-4 text-xl font-bold">Idiomas</h2>
                            <div class="space-y-2">
                                @foreach($data['languages'] as $lang)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">{{ $lang['name'] }}</span>
                                    <span class="rounded bg-cyan-400/20 px-2 py-1 text-xs text-cyan-300">
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

        <footer class="border-t border-blue-400/20 py-8 text-center">
            <p class="text-gray-400">
                Desarrollado con 💻 • {{ date('Y') }}
            </p>
        </footer>
    </div>
</body>
</html>