<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Portfolio;
use App\Models\PortfolioSection;

class TemplateController extends Controller
{
    public function Template() 
    {
        $templates = [
            [
                'id' => 'moderna',
                'name' => 'Moderna',
                'description' => 'Diseño contemporáneo con gradientes y elementos visuales impactantes',
                'category' => 'Profesional',
                'color' => 'from-blue-500 to-cyan-500',
                'popular' => true,
                'features' => ['Responsive', 'Gradientes', 'Moderno'],
                'preview_component' => 'Moderna'
            ],
            [
                'id' => 'minimalista',
                'name' => 'Minimalista',
                'description' => 'Enfoque en el contenido con diseño limpio y elegante',
                'category' => 'Clásico',
                'color' => 'from-gray-600 to-gray-800',
                'popular' => false,
                'features' => ['Limpio', 'Elegante', 'Fácil lectura'],
                'preview_component' => 'Minimalista'
            ],
            [
                'id' => 'ejecutiva',
                'name' => 'Ejecutiva',
                'description' => 'Diseño corporativo para profesionales de alto nivel',
                'category' => 'Ejecutivo',
                'color' => 'from-slate-700 to-slate-900',
                'popular' => true,
                'features' => ['Corporativo', 'Profesional', 'Liderazgo'],
                'preview_component' => 'Ejecutiva'
            ],
            [
                'id' => 'creativa',
                'name' => 'Creativa',
                'description' => 'Perfecta para diseñadores y profesionales creativos',
                'category' => 'Creativo',
                'color' => 'from-purple-500 to-pink-500',
                'popular' => false,
                'features' => ['Colorido', 'Creativo', 'Visual'],
                'preview_component' => 'Creativa'
            ],
            [
                'id' => 'tecnologica',
                'name' => 'Tecnológica',
                'description' => 'Enfoque en habilidades técnicas y proyectos de desarrollo',
                'category' => 'Tecnología',
                'color' => 'from-blue-900 to-indigo-900',
                'popular' => true,
                'features' => ['Tech', 'Desarrollo', 'Moderno'],
                'preview_component' => 'Tecnologica'
            ]
        ];

        return Inertia::render('Dashboard/SelectTemplate', [
            'templates' => $templates
        ]);
    }

    public function createPortfolio(Request $request)
    {
        $request->validate([
            'template_type' => 'required|string'
        ]);

        // Crear el portfolio con la plantilla seleccionada
        $portfolio = Portfolio::create([
            'user_id' => auth()->id(),
            'title' => 'Mi Portafolio - ' . ucfirst($request->template_type),
            'slug' => 'mi-portafolio-' . uniqid(),
            'description' => 'Mi portafolio profesional creado con PortafolioAI',
            'template_type' => $request->template_type,
            'template_data' => $this->getTemplateDefaultData($request->template_type),
            'is_public' => false,
            'is_active' => true,
            'theme_settings' => $this->getDefaultThemeSettings($request->template_type),
        ]);

        // Crear secciones por defecto basadas en la plantilla
        $this->createDefaultSections($portfolio, $request->template_type);

        return redirect()->route('dashboard.portfolio.editor', ['portfolio' => $portfolio->id]);
    }

    public function EditorTemplate($portfolioId)
    {
        $portfolio = Portfolio::with('sections')->findOrFail($portfolioId);
        
        // Verificar que el usuario sea el propietario
        if ($portfolio->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('Dashboard/Porfolios/Editor', [
            'portfolio' => $portfolio,
            'templateData' => $portfolio->template_data,
            'sections' => $portfolio->sections
        ]);
    }

    private function getTemplateDefaultData($templateType)
    {
        $defaultData = [
            'personal' => [
                'name' => auth()->user()->name,
                'title' => 'Tu título profesional',
                'email' => auth()->user()->email,
                'phone' => '',
                'location' => '',
                'website' => '',
                'linkedin' => '',
                'github' => '',
                'summary' => 'Breve resumen sobre tu experiencia y habilidades...',
            ],
            
            'experience' => [],
            'education' => [],
            'skills' => [
                'technical' => [],
                'soft' => []
            ],
            'projects' => [],
            'certifications' => [],
            'languages' => []
        ];

        // Datos específicos por plantilla
        switch ($templateType) {
            case 'tecnologica':
                $defaultData['personal']['title'] = 'Desarrollador de Software';
                $defaultData['skills']['technical'] = ['JavaScript', 'PHP', 'Vue.js', 'Laravel'];
                break;
            case 'ejecutiva':
                $defaultData['personal']['title'] = 'Director Ejecutivo';
                $defaultData['skills']['soft'] = ['Liderazgo', 'Gestión', 'Estrategia'];
                break;
            case 'creativa':
                $defaultData['personal']['title'] = 'Diseñador Creativo';
                $defaultData['skills']['technical'] = ['Figma', 'Adobe Creative Suite', 'UI/UX'];
                break;
        }

        return $defaultData;
    }

    private function getDefaultThemeSettings($templateType)
    {
        $themes = [
            'moderna' => ['primary_color' => '#005aeb', 'font_family' => 'Inter'],
            'minimalista' => ['primary_color' => '#374151', 'font_family' => 'Crimson Text'],
            'ejecutiva' => ['primary_color' => '#1e293b', 'font_family' => 'Inter'],
            'creativa' => ['primary_color' => '#7c3aed', 'font_family' => 'Inter'],
            'tecnologica' => ['primary_color' => '#005aeb', 'font_family' => 'Inter']
        ];

        return $themes[$templateType] ?? $themes['moderna'];
    }

    private function createDefaultSections($portfolio, $templateType)
    {
        $sections = [
            [
                'component_type' => 'personal', 
                'section_name' => 'Información Personal', 
                'order' => 1, 
                'is_visible' => true,
                'content' => json_encode([]) // Proporcionar contenido vacío por defecto
            ],
            [
                'component_type' => 'about', 
                'section_name' => 'Sobre Mí', 
                'order' => 2, 
                'is_visible' => true,
                'content' => json_encode([])
            ],
            [
                'component_type' => 'experience', 
                'section_name' => 'Experiencia', 
                'order' => 3, 
                'is_visible' => true,
                'content' => json_encode([])
            ],
            [
                'component_type' => 'skills', 
                'section_name' => 'Habilidades', 
                'order' => 4, 
                'is_visible' => true,
                'content' => json_encode([])
            ],
            [
                'component_type' => 'projects', 
                'section_name' => 'Proyectos', 
                'order' => 5, 
                'is_visible' => true,
                'content' => json_encode([])
            ],
            [
                'component_type' => 'education', 
                'section_name' => 'Educación', 
                'order' => 6, 
                'is_visible' => true,
                'content' => json_encode([])
            ],
        ];

        foreach ($sections as $section) {
            $portfolio->sections()->create($section);
        }
    }

    // Método para actualizar el portfolio


public function updatePortfolio(Request $request, Portfolio $portfolio)
{
    if ($portfolio->user_id !== auth()->id()) {
        abort(403);
    }

    $portfolio->update([
        'template_data' => $request->template_data,
    ]);

    return back()->with('success', 'Portfolio actualizado correctamente');
}

}