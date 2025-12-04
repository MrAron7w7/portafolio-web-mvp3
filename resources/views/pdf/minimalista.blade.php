<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['personal']['name'] ?? 'CV Minimalista' }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:wght@400;600&display=swap" rel="stylesheet">

    <style>
        @page { margin: 0px; }
        body { 
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact;
            margin: 0;
            padding: 0;
        }
        /* Clase personalizada para la fuente serif del diseño minimalista */
        .font-serif {
            font-family: 'Crimson Text', serif;
        }
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
                $months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                $monthIndex = (int)$parts[1] - 1;
                return ($months[$monthIndex] ?? '') . ' ' . $parts[0];
            }
        }
    @endphp

    <div class="min-h-screen bg-white">
        <div class="mx-auto max-w-4xl px-8 py-16">
            
            <header class="mb-16 border-b border-gray-900 pb-8">
                <h1 class="mb-2 font-serif text-5xl font-light tracking-tight text-gray-900">
                    {{ $data['personal']['name'] ?? 'Tu Nombre' }}
                </h1>
                <p class="mb-6 text-xl font-light text-gray-600">
                    {{ $data['personal']['title'] ?? 'Tu Título Profesional' }}
                </p>

                <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-600">
                    @if(!empty($data['personal']['email']))
                    <a href="mailto:{{ $data['personal']['email'] }}" class="hover:text-gray-900">
                        {{ $data['personal']['email'] }}
                    </a>
                    @endif

                    @if(!empty($data['personal']['phone']))
                    <span>{{ $data['personal']['phone'] }}</span>
                    @endif

                    @if(!empty($data['personal']['location']))
                    <span>{{ $data['personal']['location'] }}</span>
                    @endif

                    @if(!empty($data['personal']['website']))
                    <a href="{{ $data['personal']['website'] }}" target="_blank" class="hover:text-gray-900">
                        Sitio Web
                    </a>
                    @endif

                    @if(!empty($data['personal']['linkedin']))
                    <a href="{{ $data['personal']['linkedin'] }}" target="_blank" class="hover:text-gray-900">
                        LinkedIn
                    </a>
                    @endif

                    @if(!empty($data['personal']['github']))
                    <a href="{{ $data['personal']['github'] }}" target="_blank" class="hover:text-gray-900">
                        GitHub
                    </a>
                    @endif
                </div>
            </header>

            @if(!empty($data['personal']['summary']))
            <section class="mb-16">
                <p class="font-serif text-lg leading-relaxed text-gray-700">
                    {{ $data['personal']['summary'] }}
                </p>
            </section>
            @endif

            @if(!empty($data['experience']))
            <section class="mb-16">
                <h2 class="mb-8 font-serif text-2xl font-light text-gray-900">
                    Experiencia
                </h2>

                <div class="space-y-10">
                    @foreach($data['experience'] as $exp)
                    <div>
                        <div class="mb-2 flex items-baseline justify-between">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $exp['position'] }}
                            </h3>
                            <span class="text-sm text-gray-500">
                                {{ formatDate($exp['startDate']) }} – 
                                {{ ($exp['current'] ?? false) ? 'Presente' : formatDate($exp['endDate'] ?? '') }}
                            </span>
                        </div>
                        <p class="mb-3 text-gray-600">{{ $exp['company'] }}</p>
                        <p class="leading-relaxed text-gray-700">
                            {{ $exp['description'] }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            @if(!empty($data['education']))
            <section class="mb-16">
                <h2 class="mb-8 font-serif text-2xl font-light text-gray-900">
                    Educación
                </h2>

                <div class="space-y-6">
                    @foreach($data['education'] as $edu)
                    <div>
                        <div class="mb-2 flex items-baseline justify-between">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $edu['degree'] }}
                            </h3>
                            <span class="text-sm text-gray-500">
                                {{ formatDate($edu['startDate']) }} – 
                                {{ formatDate($edu['endDate']) }}
                            </span>
                        </div>
                        <p class="text-gray-600">{{ $edu['institution'] }}</p>
                        @if(!empty($edu['field']))
                        <p class="text-sm text-gray-500">
                            {{ $edu['field'] }}
                        </p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            @if(!empty($data['skills']['technical']) || !empty($data['skills']['soft']))
            <section class="mb-16">
                <h2 class="mb-8 font-serif text-2xl font-light text-gray-900">
                    Habilidades
                </h2>

                <div class="space-y-4">
                    @if(!empty($data['skills']['technical']))
                    <div>
                        <h3 class="mb-2 text-sm font-medium tracking-wide text-gray-500 uppercase">
                            Técnicas
                        </h3>
                        <p class="leading-relaxed text-gray-700">
                            {{-- implode es el equivalente en PHP a .join() de JS --}}
                            {{ implode(' • ', $data['skills']['technical']) }}
                        </p>
                    </div>
                    @endif

                    @if(!empty($data['skills']['soft']))
                    <div>
                        <h3 class="mb-2 text-sm font-medium tracking-wide text-gray-500 uppercase">
                            Competencias
                        </h3>
                        <p class="leading-relaxed text-gray-700">
                            {{ implode(' • ', $data['skills']['soft']) }}
                        </p>
                    </div>
                    @endif
                </div>
            </section>
            @endif

            @if(!empty($data['projects']))
            <section class="mb-16">
                <h2 class="mb-8 font-serif text-2xl font-light text-gray-900">
                    Proyectos
                </h2>

                <div class="space-y-8">
                    @foreach($data['projects'] as $project)
                    <div>
                        <h3 class="mb-2 text-lg font-medium text-gray-900">
                            {{ $project['name'] }}
                        </h3>
                        <p class="mb-2 leading-relaxed text-gray-700">
                            {{ $project['description'] }}
                        </p>
                        
                        @if(!empty($project['technologies']))
                        <p class="text-sm text-gray-500">
                            {{ implode(' • ', $project['technologies']) }}
                        </p>
                        @endif

                        @if(!empty($project['url']))
                        <a href="{{ $project['url'] }}" target="_blank" class="text-sm text-gray-900 underline">
                            Ver proyecto
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            @if(!empty($data['languages']))
            <section>
                <h2 class="mb-8 font-serif text-2xl font-light text-gray-900">
                    Idiomas
                </h2>

                <div class="grid grid-cols-2 gap-4">
                    @foreach($data['languages'] as $lang)
                    <div>
                        <span class="text-gray-900">{{ $lang['name'] }}</span>
                        <span class="text-gray-500"> — {{ $lang['level'] }}</span>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

        </div>

        <footer class="border-t border-gray-200 bg-white py-8 text-center">
            <p class="text-gray-600">
                Generado con PortafolioAI • {{ date('Y') }}
            </p>
        </footer>
    </div>
</body>
</html>