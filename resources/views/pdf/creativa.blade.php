<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['personal']['name'] ?? 'CV Creativo' }}</title>
    
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
        /* Clases personalizadas extraídas del style scoped */
        .transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .hover-scale:hover { transform: scale(1.05); }
    </style>
</head>
<body>

    {{-- LÓGICA PHP (Helper para fechas) --}}
    @php
        if (!function_exists('formatDate')) {
            function formatDate($date) {
                if (!$date) return '';
                $parts = explode('-', $date);
                if(count($parts) < 2) return $date;
                // Formato MM/YYYY
                return $parts[1] . '/' . $parts[0];
            }
        }
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50">
        <header class="relative overflow-hidden bg-gradient-to-r from-purple-600 to-pink-500 px-8 py-16 text-white">
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-10 left-10">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-24 w-24">
                        <circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/>
                    </svg>
                </div>
                <div class="absolute right-10 bottom-10">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-24 w-24">
                        <path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/><path d="M20 3v4"/><path d="M22 5h-4"/><path d="M4 17v2"/><path d="M5 18H3"/>
                    </svg>
                </div>
            </div>

            <div class="relative mx-auto max-w-4xl text-center">
                @if(!empty($data['personal']['photo']))
                <div class="mb-6 inline-block">
                    <div class="relative">
                        <div class="h-32 w-32 overflow-hidden rounded-full border-4 border-white shadow-2xl">
                            <img src="{{ $data['personal']['photo'] }}" alt="{{ $data['personal']['name'] }}" class="h-full w-full object-cover" />
                        </div>
                        <div class="absolute -right-2 -bottom-2 rounded-full bg-yellow-400 p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-white">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                        </div>
                    </div>
                </div>
                @endif

                <h1 class="mb-4 text-5xl font-bold">
                    {{ $data['personal']['name'] ?? 'Tu Nombre' }}
                </h1>
                <p class="mb-6 text-2xl text-purple-100">
                    {{ $data['personal']['title'] ?? 'Tu Título Creativo' }}
                </p>

                <div class="flex flex-wrap justify-center gap-4">
                    @if(!empty($data['personal']['email']))
                    <a href="mailto:{{ $data['personal']['email'] }}" class="rounded-full bg-white/20 px-6 py-3 backdrop-blur-sm transition hover:bg-white/30">
                        ✉️ {{ $data['personal']['email'] }}
                    </a>
                    @endif

                    @if(!empty($data['personal']['phone']))
                    <div class="rounded-full bg-white/20 px-6 py-3 backdrop-blur-sm">
                        📞 {{ $data['personal']['phone'] }}
                    </div>
                    @endif

                    @if(!empty($data['personal']['location']))
                    <div class="rounded-full bg-white/20 px-6 py-3 backdrop-blur-sm">
                        📍 {{ $data['personal']['location'] }}
                    </div>
                    @endif
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-4xl px-8 py-12">
            
            @if(!empty($data['personal']['summary']))
            <section class="mb-12">
                <div class="relative rounded-3xl bg-white p-8 shadow-xl">
                    <div class="absolute -top-4 left-8 rounded-full bg-pink-500 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-white">
                            <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                        </svg>
                    </div>
                    <h2 class="mb-4 text-2xl font-bold text-gray-900">
                        Mi Historia
                    </h2>
                    <p class="text-lg leading-relaxed text-gray-700">
                        {{ $data['personal']['summary'] }}
                    </p>
                </div>
            </section>
            @endif

            <div class="grid gap-8 lg:grid-cols-2">
                
                @if(!empty($data['experience']))
                <section class="lg:col-span-2">
                    <h2 class="mb-8 text-center text-3xl font-bold text-gray-900">
                        🎨 Experiencia Profesional
                    </h2>

                    <div class="space-y-6">
                        @foreach($data['experience'] as $index => $exp)
                        <div class="relative rounded-2xl bg-white p-6 shadow-lg transition-transform hover:scale-105 {{ $index % 2 === 0 ? 'border-l-4 border-purple-500' : 'border-l-4 border-pink-500' }}">
                            <div class="mb-3 flex items-start justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">
                                        {{ $exp['position'] }}
                                    </h3>
                                    <p class="text-lg text-purple-600">
                                        {{ $exp['company'] }}
                                    </p>
                                </div>
                                <span class="rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-1 text-sm text-white">
                                    {{ formatDate($exp['startDate']) }} – 
                                    {{ ($exp['current'] ?? false) ? 'Ahora' : formatDate($exp['endDate'] ?? '') }}
                                </span>
                            </div>
                            <p class="leading-relaxed text-gray-700">
                                {{ $exp['description'] }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                </section>
                @endif

                @if(!empty($data['skills']['technical']) || !empty($data['skills']['soft']))
                <section>
                    <div class="rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 p-8 text-white">
                        <h2 class="mb-6 text-2xl font-bold">
                            Mis Superpoderes
                        </h2>

                        @if(!empty($data['skills']['technical']))
                        <div class="mb-6">
                            <h3 class="mb-3 text-lg font-semibold">
                                💻 Habilidades Técnicas
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($data['skills']['technical'] as $skill)
                                <span class="rounded-full bg-white/20 px-4 py-2 text-sm backdrop-blur-sm">
                                    {{ $skill }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if(!empty($data['skills']['soft']))
                        <div>
                            <h3 class="mb-3 text-lg font-semibold">
                                🌟 Habilidades Blandas
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($data['skills']['soft'] as $skill)
                                <span class="rounded-full bg-white/20 px-4 py-2 text-sm backdrop-blur-sm">
                                    {{ $skill }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </section>
                @endif

                @if(!empty($data['projects']))
                <section>
                    <div class="rounded-2xl bg-white p-8 shadow-lg">
                        <h2 class="mb-6 text-2xl font-bold text-gray-900">
                            ✨ Proyectos Destacados
                        </h2>

                        <div class="space-y-4">
                            @foreach($data['projects'] as $project)
                            <div class="rounded-xl border-2 border-dashed border-purple-200 p-4 transition hover:border-purple-400">
                                <h3 class="font-bold text-gray-900">
                                    {{ $project['name'] }}
                                </h3>
                                <p class="mb-2 text-sm text-gray-600">
                                    {{ $project['description'] }}
                                </p>
                                @if(!empty($project['technologies']))
                                <div class="flex flex-wrap gap-1">
                                    @foreach($project['technologies'] as $tech)
                                    <span class="rounded-full bg-purple-100 px-2 py-1 text-xs text-purple-700">
                                        {{ $tech }}
                                    </span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </section>
                @endif

                @if(!empty($data['education']))
                <section class="lg:col-span-2">
                    <div class="rounded-2xl bg-white p-8 shadow-lg">
                        <h2 class="mb-6 text-2xl font-bold text-gray-900">
                            🎓 Trayectoria Académica
                        </h2>

                        <div class="grid gap-4 md:grid-cols-2">
                            @foreach($data['education'] as $edu)
                            <div class="rounded-xl bg-gradient-to-r from-purple-50 to-pink-50 p-6">
                                <h3 class="font-bold text-gray-900">
                                    {{ $edu['degree'] }}
                                </h3>
                                <p class="text-purple-600">
                                    {{ $edu['institution'] }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ formatDate($edu['startDate']) }} - 
                                    {{ formatDate($edu['endDate']) }}
                                </p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </section>
                @endif
            </div>
        </main>

        <footer class="border-t border-purple-200 bg-white py-8 text-center">
            <div class="mb-4 flex justify-center space-x-4">
                @if(!empty($data['personal']['website']))
                <a href="{{ $data['personal']['website'] }}" target="_blank" class="rounded-full bg-purple-100 p-3 text-purple-600 transition hover:bg-purple-200">
                    🌐 Sitio Web
                </a>
                @endif

                @if(!empty($data['personal']['linkedin']))
                <a href="{{ $data['personal']['linkedin'] }}" target="_blank" class="rounded-full bg-blue-100 p-3 text-blue-600 transition hover:bg-blue-200">
                    💼 LinkedIn
                </a>
                @endif

                @if(!empty($data['personal']['github']))
                <a href="{{ $data['personal']['github'] }}" target="_blank" class="rounded-full bg-gray-100 p-3 text-gray-600 transition hover:bg-gray-200">
                    💻 GitHub
                </a>
                @endif
            </div>
            <p class="text-gray-600">
                Creado con 💜 • {{ date('Y') }}
            </p>
        </footer>
    </div>
</body>
</html>