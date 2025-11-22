<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Ajustes para impresión */
        @page { margin: 0px; }
        body { -webkit-print-color-adjust: exact; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800">

    <div class="max-w-4xl mx-auto bg-white min-h-screen shadow-lg overflow-hidden">
        
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-10 text-white text-center">
            <div class="w-32 h-32 bg-white rounded-full mx-auto mb-4 border-4 border-white overflow-hidden flex items-center justify-center text-gray-500">
                <span class="text-4xl font-bold">{{ substr($portfolio->user->name, 0, 1) }}</span>
            </div>
            
            <h1 class="text-4xl font-bold uppercase tracking-wider">{{ $portfolio->user->name }}</h1>
            <p class="text-indigo-100 text-lg mt-2">{{ $portfolio->title }}</p>
            
            <div class="mt-6 flex justify-center gap-4 text-sm opacity-90">
                <span>📧 {{ $portfolio->user->email }}</span>
                @if($portfolio->custom_domain)
                    <span>🌐 {{ $portfolio->custom_domain }}</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-3 gap-0">
            
            <div class="col-span-1 bg-gray-50 p-8 border-r border-gray-200">
                
                <div class="mb-8">
                    <h3 class="text-indigo-600 font-bold uppercase tracking-widest border-b-2 border-indigo-200 pb-2 mb-4">Skills</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($portfolio->sections as $section)
                            @if($section->component_type == 'skills' && isset($section->content['skills']))
                                @foreach($section->content['skills'] as $skill)
                                    <span class="bg-white border border-gray-300 px-3 py-1 rounded-md text-xs font-semibold text-gray-600 shadow-sm">
                                        {{ $skill }}
                                    </span>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                </div>

                @if($portfolio->certificates->count() > 0)
                    <div>
                        <h3 class="text-indigo-600 font-bold uppercase tracking-widest border-b-2 border-indigo-200 pb-2 mb-4">Logros</h3>
                        <ul class="space-y-3">
                        @foreach($portfolio->certificates as $cert)
                            <li class="text-sm">
                                <strong class="block text-gray-700">{{ $cert->title }}</strong>
                                <span class="text-gray-500 text-xs">{{ $cert->issuer }} • {{ $cert->issued_at->format('Y') }}</span>
                            </li>
                        @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="col-span-2 p-8">
                <h3 class="text-indigo-600 font-bold uppercase tracking-widest border-b-2 border-indigo-200 pb-2 mb-6">Experiencia Laboral</h3>

                @foreach($portfolio->sections as $section)
                    @if($section->component_type == 'experience_list' && isset($section->content['items']))
                        <div class="space-y-8">
                        @foreach($section->content['items'] as $item)
                            <div class="relative pl-4 border-l-2 border-gray-200">
                                <div class="absolute -left-[9px] top-0 w-4 h-4 bg-indigo-500 rounded-full border-4 border-white shadow"></div>
                                
                                <div class="flex justify-between items-baseline mb-1">
                                    <h4 class="text-lg font-bold text-gray-800">{{ $item['role'] }}</h4>
                                    <span class="text-xs font-bold text-indigo-500 bg-indigo-50 px-2 py-1 rounded">
                                        {{ $item['start_date'] }} - {{ $item['end_date'] }}
                                    </span>
                                </div>
                                <div class="text-md font-semibold text-gray-600 mb-2">{{ $item['company'] }}</div>
                                <p class="text-sm text-gray-500 leading-relaxed text-justify">
                                    {{ $item['description'] }}
                                </p>
                            </div>
                        @endforeach
                        </div>
                    @endif
                @endforeach

                <div class="mt-10 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <h4 class="font-bold text-yellow-800 mb-2 text-sm">Sobre mí</h4>
                    <p class="text-sm text-yellow-700 leading-relaxed">{{ $portfolio->description }}</p>
                </div>
            </div>

        </div>
    </div>
</body>
</html>