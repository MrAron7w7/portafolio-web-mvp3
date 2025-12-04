<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['personal']['name'] ?? 'CV Académico' }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700&display=swap" rel="stylesheet">

    <style>
        @page { margin: 0px; }
        body { 
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact;
            margin: 0;
            padding: 0;
        }
        /* Clase personalizada para la fuente serif */
        .font-serif {
            font-family: 'Merriweather', serif;
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
                
                // Evitar errores si el mes viene mal
                $monthName = $months[$monthIndex] ?? '';
                
                return $monthName . ' ' . $parts[0];
            }
        }
    @endphp

    <div class="min-h-screen bg-white">
        <div class="mx-auto max-w-5xl px-8 py-12">
            
            <header class="mb-12 border-b border-gray-300 pb-8">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="mb-2 text-4xl font-bold text-gray-900 font-serif">
                            {{ $data['personal']['name'] ?? 'Tu Nombre' }}
                        </h1>
                        <p class="mb-4 text-xl text-gray-600">
                            {{ $data['personal']['title'] ?? 'Tu Título Académico' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="space-y-1 text-sm text-gray-600">
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
                        </div>
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-4 gap-8">
                <div class="col-span-3">
                    
                    @if(!empty($data['personal']['summary']))
                    <section class="mb-8">
                        <div class="mb-4 flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-1 mr-3 h-5 w-5 text-blue-600">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                            </svg>
                            <h2 class="text-2xl font-bold text-gray-900 font-serif">
                                Perfil Académico
                            </h2>
                        </div>
                        <p class="leading-relaxed text-gray-700">
                            {{ $data['personal']['summary'] }}
                        </p>
                    </section>
                    @endif

                    @if(!empty($data['experience']))
                    <section class="mb-8">
                        <div class="mb-4 flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-1 mr-3 h-5 w-5 text-blue-600">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <h2 class="text-2xl font-bold text-gray-900 font-serif">
                                Experiencia Académica
                            </h2>
                        </div>
                        <div class="space-y-6">
                            @foreach($data['experience'] as $exp)
                            <div class="border-l-2 border-blue-600 pl-4">
                                <div class="mb-2 flex items-start justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $exp['position'] }}
                                    </h3>
                                    <span class="text-sm whitespace-nowrap text-gray-500">
                                        {{ formatDate($exp['startDate']) }} – 
                                        {{ ($exp['current'] ?? false) ? 'Presente' : formatDate($exp['endDate'] ?? '') }}
                                    </span>
                                </div>
                                <p class="mb-2 font-medium text-blue-600">
                                    {{ $exp['company'] }}
                                </p>
                                <p class="text-gray-700">
                                    {{ $exp['description'] }}
                                </p>
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif

                    @if(!empty($data['research']))
                    <section class="mb-8">
                        <div class="mb-4 flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-1 mr-3 h-5 w-5 text-blue-600">
                                <circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>
                            </svg>
                            <h2 class="text-2xl font-bold text-gray-900 font-serif">
                                Investigación
                            </h2>
                        </div>
                        <div class="space-y-4">
                            @foreach($data['research'] as $research)
                            <div class="rounded-lg bg-gray-50 p-4">
                                <h3 class="mb-1 font-semibold text-gray-900">
                                    {{ $research['title'] }}
                                </h3>
                                <p class="mb-2 text-sm text-blue-600">
                                    {{ $research['institution'] }} • {{ $research['period'] }}
                                </p>
                                <p class="text-sm text-gray-700">
                                    {{ $research['description'] }}
                                </p>
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif

                    @if(!empty($data['publications']))
                    <section class="mb-8">
                        <h2 class="mb-4 text-2xl font-bold text-gray-900 font-serif">
                            Publicaciones
                        </h2>
                        <div class="space-y-3">
                            @foreach($data['publications'] as $pub)
                            <div class="border-b border-gray-200 pb-3">
                                <h3 class="font-semibold text-gray-900">
                                    {{ $pub['title'] }}
                                </h3>
                                <p class="text-sm text-blue-600">
                                    {{ $pub['journal'] }} • {{ $pub['date'] }}
                                </p>
                                @if(!empty($pub['link']))
                                <a href="{{ $pub['link'] }}" target="_blank" class="text-sm text-gray-600 hover:text-blue-600">
                                    Enlace a la publicación
                                </a>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif
                </div>

                <div class="col-span-1">
                    
                    @if(!empty($data['education']))
                    <section class="mb-8">
                        <div class="mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-5 w-5 text-blue-600">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                            </svg>
                            <h2 class="text-xl font-bold text-gray-900 font-serif">
                                Educación
                            </h2>
                        </div>
                        <div class="space-y-4">
                            @foreach($data['education'] as $edu)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">
                                    {{ $edu['degree'] }}
                                </h3>
                                <p class="text-xs text-blue-600">
                                    {{ $edu['institution'] }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ formatDate($edu['startDate']) }} - 
                                    {{ formatDate($edu['endDate']) }}
                                </p>
                                @if(!empty($edu['field']))
                                <p class="text-xs text-gray-600">
                                    {{ $edu['field'] }}
                                </p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif

                    @if(!empty($data['skills']['technical']))
                    <section class="mb-8">
                        <h2 class="mb-4 text-xl font-bold text-gray-900 font-serif">
                            Competencias
                        </h2>
                        <div class="space-y-2">
                            @foreach($data['skills']['technical'] as $skill)
                            <div class="flex items-center">
                                <div class="mr-2 h-2 w-2 rounded-full bg-blue-600"></div>
                                <span class="text-sm text-gray-700">{{ $skill }}</span>
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif

                    @if(!empty($data['languages']))
                    <section class="mb-8">
                        <h2 class="mb-4 text-xl font-bold text-gray-900 font-serif">
                            Idiomas
                        </h2>
                        <div class="space-y-2">
                            @foreach($data['languages'] as $lang)
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-700">{{ $lang['name'] }}</span>
                                <span class="text-sm text-gray-500">{{ $lang['level'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif

                    @if(!empty($data['certifications']))
                    <section class="mb-8">
                        <h2 class="mb-4 text-xl font-bold text-gray-900 font-serif">
                            Certificaciones
                        </h2>
                        <div class="space-y-2">
                            @foreach($data['certifications'] as $cert)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">
                                    {{ $cert['name'] }}
                                </h3>
                                <p class="text-xs text-gray-500">
                                    {{ $cert['issuer'] }} • {{ $cert['date'] }}
                                </p>
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</body>
</html>