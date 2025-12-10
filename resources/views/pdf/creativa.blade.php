<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1280, initial-scale=1.0">
    <title>{{ $data['personal']['name'] ?? 'CV Creativo' }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* ============================================
           BASE
           ============================================ */
        @page { margin: 0px; }
        body { 
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact;
            margin: 0;
            padding: 0;
            background-color: #fdf2f8; /* bg-pink-50 */
            color: #1f2937;
            font-size: 12px;
            line-height: 1.5;
        }

        /* HEADER */
        .header {
            position: relative;
            background: linear-gradient(to right, #9333ea, #ec4899); /* purple-600 -> pink-500 */
            padding: 3rem 2rem;
            text-align: center;
            color: white;
            margin-bottom: 2rem;
        }
        .profile-photo {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            object-fit: cover;
            margin: 0 auto 1rem auto;
            display: block;
            background-color: white;
        }
        .header-name {
            font-size: 32px;
            font-weight: 800;
            margin: 0 0 0.5rem 0;
            line-height: 1.1;
        }
        .header-title {
            font-size: 16px;
            color: #fce7f3; /* pink-100 */
            margin: 0 0 1rem 0;
            font-weight: 500;
        }
        .contact-pill {
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 0.4rem 1rem;
            border-radius: 9999px;
            font-size: 11px;
            margin: 0 0.25rem;
            color: white;
            text-decoration: none;
            backdrop-filter: blur(4px);
        }

        /* CONTENEDOR PRINCIPAL */
        .container {
            padding: 0 3rem 3rem 3rem;
            max-width: 100%;
        }

        /* RESUMEN */
        .summary-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            position: relative;
        }
        .section-title {
            font-size: 18px;
            font-weight: 800;
            color: #111827;
            margin: 0 0 1rem 0;
        }
        .section-title-center { text-align: center; margin-bottom: 1.5rem; font-size: 20px; }

        /* EXPERIENCIA (Ancho Completo) */
        .exp-card {
            background: white;
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 1.25rem;
            position: relative;
        }
        /* Bordes alternados como en Vue */
        .border-purple { border-left: 5px solid #9333ea; }
        .border-pink { border-left: 5px solid #ec4899; }

        .exp-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }
        .exp-role { font-size: 14px; font-weight: 700; color: #1f2937; margin: 0; }
        .exp-company { font-size: 13px; color: #9333ea; margin: 0; font-weight: 600; }
        .exp-date {
            background: linear-gradient(to right, #9333ea, #ec4899);
            color: white;
            padding: 0.2rem 0.8rem;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 600;
            white-space: nowrap;
        }
        .exp-desc { color: #4b5563; font-size: 12px; margin: 0.5rem 0 0 0; text-align: justify; }

        /* DOS COLUMNAS (Skills | Proyectos) */
        .split-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 1.5rem 0; /* Espacio horizontal */
            margin-left: -1.5rem; /* Compensar margen izquierdo */
            margin-bottom: 2rem;
        }
        .col-half {
            width: 50%;
            vertical-align: top;
            padding-left: 1.5rem;
        }

        /* SKILLS */
        .skills-card {
            background: linear-gradient(to bottom right, #9333ea, #ec4899);
            color: white;
            border-radius: 1rem;
            padding: 1.5rem;
            height: 100%; /* Llenar altura */
        }
        .skills-header { font-size: 16px; font-weight: 700; margin: 0 0 1rem 0; color: white; }
        .skill-cat { font-size: 12px; font-weight: 600; margin-bottom: 0.5rem; opacity: 0.9; }
        .skill-tag {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            font-size: 11px;
            margin: 0 0.25rem 0.25rem 0;
        }

        /* PROYECTOS */
        .projects-container {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            height: 100%;
        }
        .project-item {
            border: 2px dashed #e9d5ff; /* purple-200 */
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .proj-name { font-size: 13px; font-weight: 700; margin: 0 0 0.25rem 0; color: #1f2937; }
        .proj-desc { font-size: 11px; color: #6b7280; margin: 0 0 0.5rem 0; }
        .proj-tech {
            display: inline-block;
            background: #f3e8ff;
            color: #7e22ce;
            padding: 0.1rem 0.4rem;
            border-radius: 999px;
            font-size: 10px;
            margin-right: 0.25rem;
        }

        /* EDUCACIÓN (Grid simulado con inline-block) */
        .edu-grid { margin-top: 1rem; }
        .edu-item {
            background: linear-gradient(to right, #faf5ff, #fdf2f8);
            border-radius: 0.75rem;
            padding: 1.25rem;
            margin-bottom: 1rem;
            width: 48%; /* Simular grid de 2 cols */
            display: inline-block;
            vertical-align: top;
            margin-right: 1%; 
        }
        .edu-degree { font-size: 13px; font-weight: 700; color: #1f2937; margin: 0; }
        .edu-inst { font-size: 12px; color: #9333ea; margin: 0.25rem 0; font-weight: 500; }
        .edu-date { font-size: 11px; color: #6b7280; }

        /* FOOTER */
        .footer {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            background: white;
            font-size: 11px;
        }

        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>

    @php
        $firstName = $data['personal']['firstName'] ?? '';
        $lastName = $data['personal']['lastName'] ?? '';
        $fullName = trim($firstName . ' ' . $lastName);
        if (empty($fullName)) { $fullName = $data['personal']['name'] ?? 'Tu Nombre'; }

        if (!function_exists('formatDate')) {
            function formatDate($date) {
                if (!$date) return '';
                $parts = explode('-', $date);
                if(count($parts) < 2) return $date;
                $months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                return ($months[(int)$parts[1] - 1] ?? '') . ' ' . $parts[0];
            }
        }
    @endphp

    <div class="min-h-screen">
        
        <header class="header">
            @if(!empty($data['personal']['photo']))
            <img src="{{ $data['personal']['photo'] }}" class="profile-photo" />
            @endif

            <h1 class="header-name">{{ $fullName }}</h1>
            <p class="header-title">{{ $data['personal']['title'] ?? 'Título Creativo' }}</p>

            <div>
                @if(!empty($data['personal']['email']))
                    <span class="contact-pill">✉️ {{ $data['personal']['email'] }}</span>
                @endif
                @if(!empty($data['personal']['phone']))
                    <span class="contact-pill">📞 {{ $data['personal']['phone'] }}</span>
                @endif
                @if(!empty($data['personal']['location']))
                    <span class="contact-pill">📍 {{ $data['personal']['location'] }}</span>
                @endif
            </div>
        </header>

        <div class="container">
            
            @if(!empty($data['personal']['summary']))
            <div class="summary-card no-break">
                <h2 class="section-title">Mi Historia</h2>
                <p style="margin:0; text-align:justify;">{{ $data['personal']['summary'] }}</p>
            </div>
            @endif

            @if(!empty($data['experience']))
            <section class="mb-8">
                <h2 class="section-title-center">🎨 Experiencia Profesional</h2>
                @foreach($data['experience'] as $index => $exp)
                <div class="exp-card {{ $index % 2 === 0 ? 'border-purple' : 'border-pink' }} no-break">
                    <div class="exp-header">
                        <div>
                            <h3 class="exp-role">{{ $exp['position'] ?? '' }}</h3>
                            <p class="exp-company">{{ $exp['company'] ?? '' }}</p>
                        </div>
                        <span class="exp-date">
                            {{ formatDate($exp['startDate'] ?? '') }} – 
                            {{ ($exp['current'] ?? false) ? 'Ahora' : formatDate($exp['endDate'] ?? '') }}
                        </span>
                    </div>
                    <p class="exp-desc">{{ $exp['description'] ?? '' }}</p>
                </div>
                @endforeach
            </section>
            @endif

            <table class="split-table no-break">
                <tr>
                    <td class="col-half">
                        @if(!empty($data['skills']['technical']) || !empty($data['skills']['soft']))
                        <div class="skills-card">
                            <h2 class="skills-header">Mis Superpoderes</h2>
                            
                            @if(!empty($data['skills']['technical']))
                            <div style="margin-bottom: 1rem;">
                                <h3 class="skill-cat">💻 Habilidades Técnicas</h3>
                                <div>
                                    @foreach($data['skills']['technical'] as $skill)
                                    <span class="skill-tag">
                                        {{ is_array($skill) ? ($skill['name'] ?? '') : $skill }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if(!empty($data['skills']['soft']))
                            <div>
                                <h3 class="skill-cat">🌟 Habilidades Blandas</h3>
                                <div>
                                    @foreach($data['skills']['soft'] as $skill)
                                    <span class="skill-tag">
                                        {{ is_array($skill) ? ($skill['name'] ?? '') : $skill }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </td>

                    <td class="col-half">
                        @if(!empty($data['projects']))
                        <div class="projects-container">
                            <h2 class="section-title">✨ Proyectos</h2>
                            @foreach($data['projects'] as $project)
                            <div class="project-item">
                                <h3 class="proj-name">{{ $project['name'] ?? 'Proyecto' }}</h3>
                                <p class="proj-desc">{{ $project['description'] ?? '' }}</p>
                                @if(!empty($project['technologies']))
                                <div>
                                    @foreach($project['technologies'] as $tech)
                                    <span class="proj-tech">{{ $tech }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </td>
                </tr>
            </table>

            @if(!empty($data['education']))
            <section class="no-break">
                <h2 class="section-title-center">🎓 Trayectoria Académica</h2>
                <div class="edu-grid">
                    @foreach($data['education'] as $edu)
                    <div class="edu-item">
                        <h3 class="edu-degree">{{ $edu['degree'] ?? '' }}</h3>
                        <p class="edu-inst">{{ $edu['institution'] ?? '' }}</p>
                        <p class="edu-date">
                            {{ formatDate($edu['startDate'] ?? '') }} - {{ formatDate($edu['endDate'] ?? '') }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

        </div>

        <footer class="footer">
            <div>
                @if(!empty($data['personal']['website'])) <span>🌐 {{ $data['personal']['website'] }}</span> @endif
                @if(!empty($data['personal']['linkedin'])) <span style="margin: 0 10px;">💼 LinkedIn</span> @endif
            </div>
            <div style="margin-top:5px;">Creado con 💜 • {{ date('Y') }}</div>
        </footer>

    </div>
</body>
</html>