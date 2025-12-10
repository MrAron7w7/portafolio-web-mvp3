<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1280, initial-scale=1.0">
    <title>{{ $data['personal']['name'] ?? 'Portafolio' }}</title>
    
    <style>
        /* ============================================
           RESET & BASE
           ============================================ */
        @page { margin: 0px; }
        * { box-sizing: border-box; }
        
        body { 
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            font-size: 12px; /* Base size reducida para encajar en A4 */
            line-height: 1.5;
            color: #374151;
        }

        /* ============================================
           LAYOUT
           ============================================ */
        .portfolio-container {
            width: 100%;
            background: white;
        }

        /* Header */
        .portfolio-header {
            position: relative;
            overflow: hidden;
            background: linear-gradient(90deg, rgb(31, 86, 204) 0%, rgb(51, 120, 247) 100%);
            padding: 2.5rem 3rem;
            color: white;
        }

        .header-decoration {
            position: absolute;
            inset: 0;
            opacity: 0.1;
        }
        .decoration-blob {
            position: absolute;
            border-radius: 50%;
            background: white;
            filter: blur(3rem);
        }
        .decoration-blob-1 { top: 0; right: 0; height: 16rem; width: 16rem; }
        .decoration-blob-2 { bottom: 0; left: 0; height: 12rem; width: 12rem; }

        .header-inner {
            display: flex;
            flex-direction: row; /* Forzamos fila */
            align-items: flex-start;
            gap: 2rem;
            position: relative;
            z-index: 10;
        }

        /* Foto */
        .profile-photo-wrapper {
            flex-shrink: 0;
        }
        .profile-photo {
            height: 120px;
            width: 120px;
            overflow: hidden;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            background: #dbeafe;
        }
        .profile-image {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }

        /* Header Info */
        .header-info { flex: 1; }
        .header-name {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 0.25rem;
        }
        .header-title {
            margin: 0 0 1rem 0;
            font-size: 16px;
            color: #dbeafe;
            font-weight: 500;
        }

        /* Contacto */
        .contact-links {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            border-radius: 0.375rem;
            background: rgba(255,255,255,0.15);
            padding: 0.35rem 0.75rem;
            font-size: 11px;
            color: white;
            text-decoration: none;
        }
        .contact-icon { width: 14px; height: 14px; }

        /* Social */
        .social-links { display: flex; gap: 0.5rem; }
        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
            background: rgba(255,255,255,0.15);
            padding: 0.4rem;
            color: white;
            text-decoration: none;
        }
        .icon { width: 16px; height: 16px; }

        /* ============================================
           MAIN CONTENT
           ============================================ */
        .portfolio-main {
            padding: 2.5rem 3rem;
        }

        /* Resumen */
        .summary-section { margin-bottom: 2rem; }
        .summary-card {
            border-radius: 0.75rem;
            background: white;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .section-title {
            margin: 0 0 0.75rem 0;
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            border-bottom: 2px solid #eff6ff;
            padding-bottom: 0.5rem;
        }
        .summary-text {
            margin: 0;
            color: #4b5563;
            font-size: 13px;
            text-align: justify;
        }

        /* GRID LAYOUT (La clave para que se vea bien) */
        .content-grid {
            display: table; /* Usamos tablas para PDF layout robusto */
            width: 100%;
            border-collapse: separate;
            border-spacing: 2rem 0; /* Espacio entre columnas */
            margin-left: -2rem; /* Compensar spacing */
        }
        .main-column {
            display: table-cell;
            width: 65%; /* Columna izquierda ancha */
            vertical-align: top;
            padding-left: 2rem;
        }
        .sidebar {
            display: table-cell;
            width: 35%; /* Columna derecha estrecha */
            vertical-align: top;
        }

        /* Secciones con Iconos */
        .section-title-with-icon {
            display: flex;
            align-items: center;
            margin: 0 0 1.25rem 0;
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .section-icon {
            margin-right: 0.5rem;
            height: 18px;
            width: 18px;
            color: #2563eb;
        }

        /* Cards de Experiencia/Proyectos */
        .experience-list, .projects-grid { display: flex; flex-direction: column; gap: 1.5rem; }
        
        .card-box {
            position: relative;
            padding-left: 1rem;
            border-left: 2px solid #e5e7eb;
        }
        
        .card-header {
            display: flex; 
            justify-content: space-between; 
            align-items: baseline;
            margin-bottom: 0.25rem;
        }
        .card-title {
            font-size: 14px;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }
        .card-subtitle {
            font-size: 13px;
            font-weight: 600;
            color: #2563eb;
            margin: 0;
        }
        .card-date {
            font-size: 11px;
            color: #6b7280;
            white-space: nowrap;
        }
        .card-desc {
            font-size: 12px;
            color: #4b5563;
            margin-top: 0.5rem;
            text-align: justify;
        }

        /* Sidebar Items */
        .sidebar-section { margin-bottom: 2rem; }
        .sidebar-title {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            text-transform: uppercase;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 0.25rem;
            margin-bottom: 1rem;
        }

        /* Tags (Skills) */
        .skills-group { margin-bottom: 1rem; }
        .skills-group-title {
            font-size: 12px;
            font-weight: 600;
            color: #4b5563;
            margin: 0 0 0.5rem 0;
        }
        .skills-tags { display: flex; flex-wrap: wrap; gap: 0.35rem; }
        .skill-tag {
            border-radius: 0.25rem;
            padding: 0.2rem 0.5rem;
            font-size: 10px;
            font-weight: 600;
        }
        .skill-tag-technical { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .skill-tag-soft { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }

        /* Educación & Idiomas en Sidebar */
        .sidebar-item { margin-bottom: 1rem; }
        .sidebar-item-title { font-weight: 700; font-size: 12px; color: #1f2937; margin: 0; }
        .sidebar-item-sub { font-size: 11px; color: #2563eb; margin: 0; }
        .sidebar-item-meta { font-size: 10px; color: #6b7280; margin: 0; }

        /* Footer */
        .portfolio-footer {
            text-align: center;
            padding: 1.5rem;
            border-top: 1px solid #e5e7eb;
            color: #9ca3af;
            font-size: 10px;
        }
        
        /* Utilidad para evitar saltos de página feos */
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>

    {{-- PHP Helpers --}}
    @php
        // Helpers para nombres y fechas
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

    <div class="portfolio-container">
        <header class="portfolio-header">
            <div class="header-decoration">
                <div class="decoration-blob decoration-blob-1"></div>
                <div class="decoration-blob decoration-blob-2"></div>
            </div>

            <div class="header-inner">
                @if(!empty($data['personal']['photo']))
                <div class="profile-photo-wrapper">
                    <div class="profile-photo">
                        <img src="{{ $data['personal']['photo'] }}" class="profile-image" />
                    </div>
                </div>
                @endif

                <div class="header-info">
                    <h1 class="header-name">{{ $fullName }}</h1>
                    <p class="header-title">{{ $data['personal']['title'] ?? '' }}</p>

                    <div class="contact-links">
                        @if(!empty($data['personal']['email']))
                        <a href="#" class="contact-item">
                            <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            {{ $data['personal']['email'] }}
                        </a>
                        @endif
                        @if(!empty($data['personal']['phone']))
                        <div class="contact-item">
                            <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            {{ $data['personal']['phone'] }}
                        </div>
                        @endif
                        @if(!empty($data['personal']['location']))
                        <div class="contact-item">
                            <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $data['personal']['location'] }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <main class="portfolio-main">
            
            @if(!empty($data['personal']['summary']))
            <section class="summary-section no-break">
                <div class="summary-card">
                    <h2 class="section-title">Perfil Profesional</h2>
                    <p class="summary-text">{{ $data['personal']['summary'] }}</p>
                </div>
            </section>
            @endif

            <div class="content-grid">
                
                <div class="main-column">
                    
                    @if(!empty($data['experience']))
                    <section class="mb-8">
                        <h2 class="section-title-with-icon">
                            <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            Experiencia Laboral
                        </h2>
                        <div class="experience-list">
                            @foreach($data['experience'] as $exp)
                            <div class="card-box no-break">
                                <div class="card-header">
                                    <h3 class="card-title">{{ $exp['position'] ?? '' }}</h3>
                                    <span class="card-date">
                                        {{ formatDate($exp['startDate'] ?? '') }} - {{ ($exp['current'] ?? false) ? 'Presente' : formatDate($exp['endDate'] ?? '') }}
                                    </span>
                                </div>
                                <p class="card-subtitle">{{ $exp['company'] ?? '' }}</p>
                                <p class="card-desc">{{ $exp['description'] ?? '' }}</p>
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif

                    @if(!empty($data['projects']))
                    <section class="mb-8">
                        <h2 class="section-title-with-icon">
                            <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                            Proyectos
                        </h2>
                        <div class="projects-grid">
                            @foreach($data['projects'] as $project)
                            <div class="card-box no-break">
                                <h3 class="card-title">{{ $project['name'] ?? 'Proyecto' }}</h3>
                                <p class="card-desc">{{ $project['description'] ?? '' }}</p>
                                @if(!empty($project['technologies']))
                                <div class="skills-tags" style="margin-top: 5px;">
                                    @foreach($project['technologies'] as $tech)
                                    <span class="skill-tag skill-tag-technical">{{ $tech }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif
                </div>

                <div class="sidebar">
                    
                    @if(!empty($data['skills']['technical']) || !empty($data['skills']['soft']))
                    <section class="sidebar-section no-break">
                        <h2 class="sidebar-title">Habilidades</h2>
                        
                        @if(!empty($data['skills']['technical']))
                        <div class="skills-group">
                            <h3 class="skills-group-title">Técnicas</h3>
                            <div class="skills-tags">
                                @foreach($data['skills']['technical'] as $skill)
                                <span class="skill-tag skill-tag-technical">
                                    {{ is_array($skill) ? ($skill['name'] ?? '') : $skill }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if(!empty($data['skills']['soft']))
                        <div class="skills-group">
                            <h3 class="skills-group-title">Blandas</h3>
                            <div class="skills-tags">
                                @foreach($data['skills']['soft'] as $skill)
                                <span class="skill-tag skill-tag-soft">
                                    {{ is_array($skill) ? ($skill['name'] ?? '') : $skill }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </section>
                    @endif

                    @if(!empty($data['education']))
                    <section class="sidebar-section no-break">
                        <h2 class="sidebar-title">Educación</h2>
                        @foreach($data['education'] as $edu)
                        <div class="sidebar-item">
                            <h3 class="sidebar-item-title">{{ $edu['degree'] ?? '' }}</h3>
                            <p class="sidebar-item-sub">{{ $edu['institution'] ?? '' }}</p>
                            <p class="sidebar-item-meta">
                                {{ formatDate($edu['startDate'] ?? '') }} - {{ formatDate($edu['endDate'] ?? '') }}
                            </p>
                        </div>
                        @endforeach
                    </section>
                    @endif

                    @if(!empty($data['languages']))
                    <section class="sidebar-section no-break">
                        <h2 class="sidebar-title">Idiomas</h2>
                        @foreach($data['languages'] as $lang)
                        <div class="sidebar-item" style="display:flex; justify-content:space-between; border-bottom:1px solid #eee; padding-bottom:2px;">
                            <span class="sidebar-item-title" style="font-weight:500;">{{ $lang['name'] ?? '' }}</span>
                            <span class="sidebar-item-meta">{{ $lang['level'] ?? '' }}</span>
                        </div>
                        @endforeach
                    </section>
                    @endif

                </div>
            </div>
        </main>

        <footer class="portfolio-footer">
            Generado con PortafolioAI • {{ date('Y') }}
        </footer>
    </div>
</body>
</html>