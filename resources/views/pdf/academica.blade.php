<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1280, initial-scale=1.0">
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
            background-color: white;
            font-size: 12px;
            color: #374151;
        }
        
        .font-serif { font-family: 'Merriweather', serif; }

        /* HEADER */
        .header {
            padding: 2.5rem 3rem;
            border-bottom: 1px solid #d1d5db;
            margin-bottom: 2rem;
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .header-name {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 0.5rem 0;
        }
        .header-title {
            font-size: 16px;
            color: #4b5563;
            margin: 0;
        }
        .contact-info {
            text-align: right;
            font-size: 11px;
            color: #4b5563;
            line-height: 1.6;
        }

        /* LAYOUT PRINCIPAL (TABLA) */
        .content-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            padding: 0 3rem 3rem 3rem;
        }
        .col-main {
            width: 70%;
            vertical-align: top;
            padding-right: 2rem;
        }
        .col-sidebar {
            width: 30%;
            vertical-align: top;
        }

        /* SECCIONES */
        .section { margin-bottom: 2rem; }
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        .section-icon {
            margin-right: 0.75rem;
            width: 18px;
            height: 18px;
            color: #2563eb;
        }
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            font-family: 'Merriweather', serif;
            margin: 0;
        }

        /* ITEMS */
        .exp-item {
            border-left: 2px solid #2563eb;
            padding-left: 1rem;
            margin-bottom: 1.5rem;
        }
        .exp-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 0.25rem;
        }
        .exp-role { font-size: 13px; font-weight: 600; color: #111827; margin: 0; }
        .exp-date { font-size: 11px; color: #6b7280; white-space: nowrap; }
        .exp-company { font-size: 12px; font-weight: 500; color: #2563eb; margin: 0 0 0.5rem 0; }
        .exp-desc { font-size: 12px; color: #374151; text-align: justify; margin: 0; }

        /* SIDEBAR ITEMS */
        .sidebar-item { margin-bottom: 1rem; }
        .sidebar-degree { font-size: 12px; font-weight: 600; color: #111827; margin: 0; }
        .sidebar-inst { font-size: 11px; color: #2563eb; margin: 0; }
        .sidebar-date { font-size: 10px; color: #6b7280; margin: 0; }

        /* SKILLS */
        .skill-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 11px;
            color: #374151;
        }
        .skill-dot {
            width: 6px;
            height: 6px;
            background-color: #2563eb;
            border-radius: 50%;
            margin-right: 0.5rem;
        }

        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>

    @php
        // Helpers
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

    <div class="min-h-screen bg-white">
        
        <header class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-name font-serif">{{ $fullName }}</h1>
                    <p class="header-title">{{ $data['personal']['title'] ?? 'Título Académico' }}</p>
                </div>
                <div class="contact-info">
                    @if(!empty($data['personal']['email']))
                        <div>{{ $data['personal']['email'] }}</div>
                    @endif
                    @if(!empty($data['personal']['phone']))
                        <div>{{ $data['personal']['phone'] }}</div>
                    @endif
                    @if(!empty($data['personal']['location']))
                        <div>{{ $data['personal']['location'] }}</div>
                    @endif
                </div>
            </div>
        </header>

        <table class="content-table">
            <tr>
                <td class="col-main">
                    
                    @if(!empty($data['personal']['summary']))
                    <section class="section">
                        <div class="section-header">
                            <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                            <h2 class="section-title">Perfil Académico</h2>
                        </div>
                        <p style="text-align: justify; margin: 0;">{{ $data['personal']['summary'] }}</p>
                    </section>
                    @endif

                    @if(!empty($data['experience']))
                    <section class="section">
                        <div class="section-header">
                            <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <h2 class="section-title">Experiencia Académica</h2>
                        </div>
                        
                        @foreach($data['experience'] as $exp)
                        <div class="exp-item no-break">
                            <div class="exp-header">
                                <h3 class="exp-role">{{ $exp['position'] ?? '' }}</h3>
                                <span class="exp-date">
                                    {{ formatDate($exp['startDate'] ?? '') }} – 
                                    {{ ($exp['current'] ?? false) ? 'Presente' : formatDate($exp['endDate'] ?? '') }}
                                </span>
                            </div>
                            <p class="exp-company">{{ $exp['company'] ?? '' }}</p>
                            <p class="exp-desc">{{ $exp['description'] ?? '' }}</p>
                        </div>
                        @endforeach
                    </section>
                    @endif

                    @if(!empty($data['research']))
                    <section class="section">
                        <div class="section-header">
                            <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                            <h2 class="section-title">Investigación</h2>
                        </div>
                        @foreach($data['research'] as $res)
                        <div class="bg-gray-50 p-4 rounded-lg mb-4 no-break">
                            <h3 style="font-weight: 600; font-size: 13px; margin: 0;">{{ $res['title'] ?? '' }}</h3>
                            <p style="font-size: 11px; color: #2563eb; margin: 2px 0 5px 0;">{{ $res['institution'] ?? '' }} • {{ $res['period'] ?? '' }}</p>
                            <p style="font-size: 11px; margin: 0;">{{ $res['description'] ?? '' }}</p>
                        </div>
                        @endforeach
                    </section>
                    @endif

                    @if(!empty($data['publications']))
                    <section class="section">
                        <h2 class="section-title" style="margin-bottom: 1rem;">Publicaciones</h2>
                        @foreach($data['publications'] as $pub)
                        <div style="border-bottom: 1px solid #eee; padding-bottom: 0.5rem; margin-bottom: 0.5rem;" class="no-break">
                            <h3 style="font-weight: 600; font-size: 13px; margin: 0;">{{ $pub['title'] ?? '' }}</h3>
                            <p style="font-size: 11px; color: #2563eb; margin: 0;">{{ $pub['journal'] ?? '' }} • {{ $pub['date'] ?? '' }}</p>
                        </div>
                        @endforeach
                    </section>
                    @endif
                </td>

                <td class="col-sidebar">
                    
                    @if(!empty($data['education']))
                    <section class="section no-break">
                        <div class="section-header">
                            <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                            <h2 class="section-title" style="font-size: 14px;">Educación</h2>
                        </div>
                        @foreach($data['education'] as $edu)
                        <div class="sidebar-item">
                            <h3 class="sidebar-degree">{{ $edu['degree'] ?? '' }}</h3>
                            <p class="sidebar-inst">{{ $edu['institution'] ?? '' }}</p>
                            <p class="sidebar-date">
                                {{ formatDate($edu['startDate'] ?? '') }} - {{ formatDate($edu['endDate'] ?? '') }}
                            </p>
                        </div>
                        @endforeach
                    </section>
                    @endif

                    @if(!empty($data['skills']['technical']))
                    <section class="section no-break">
                        <h2 class="section-title" style="font-size: 14px; margin-bottom: 0.5rem;">Competencias</h2>
                        @foreach($data['skills']['technical'] as $skill)
                        <div class="skill-item">
                            <div class="skill-dot"></div>
                            <span>{{ is_array($skill) ? ($skill['name'] ?? '') : $skill }}</span>
                        </div>
                        @endforeach
                    </section>
                    @endif

                    @if(!empty($data['languages']))
                    <section class="section no-break">
                        <h2 class="section-title" style="font-size: 14px; margin-bottom: 0.5rem;">Idiomas</h2>
                        @foreach($data['languages'] as $lang)
                        <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 4px;">
                            <span style="font-weight: 500;">{{ $lang['name'] ?? '' }}</span>
                            <span style="color: #6b7280;">{{ $lang['level'] ?? '' }}</span>
                        </div>
                        @endforeach
                    </section>
                    @endif

                    @if(!empty($data['certifications']))
                    <section class="section no-break">
                        <h2 class="section-title" style="font-size: 14px; margin-bottom: 0.5rem;">Certificaciones</h2>
                        @foreach($data['certifications'] as $cert)
                        <div class="sidebar-item">
                            <h3 class="sidebar-degree">{{ $cert['name'] ?? '' }}</h3>
                            <p class="sidebar-date">{{ $cert['issuer'] ?? '' }} • {{ $cert['date'] ?? '' }}</p>
                        </div>
                        @endforeach
                    </section>
                    @endif

                </td>
            </tr>
        </table>

    </div>
</body>
</html>