<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\Portfolio;
use App\Models\PortfolioSection;
use App\Models\LandingPageSection;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{


    private const PORTFOLIO_STORAGE_PATH = 'portfolios';
    private const PROFILE_FOLDER = 'profile';
    private const PROJECTS_FOLDER = 'projects';



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
            ],
            [
                'id' => 'academica',
                'name' => 'Académica',
                'description' => 'Ideal para investigadores, docentes y profesionales académicos',
                'category' => 'Académico',
                'color' => 'from-emerald-600 to-teal-600',
                'popular' => false,
                'features' => ['Investigación', 'Publicaciones', 'Educación'],
                'preview_component' => 'Academica'
            ]
        ];

        $sections = LandingPageSection::all()->keyBy('key');

        return Inertia::render('Dashboard/SelectTemplate', [
            'templates' => $templates,
            'sections' => $sections
        ]);
    }

    public function createPortfolio(Request $request)
    {
        $request->validate([
            'template_type' => 'required|string'
        ]);

        // Crear el portfolio con la plantilla seleccionada
        $portfolio = Portfolio::create([
            'user_id' => Auth::id(),
            'title' => 'Mi Portafolio - ' . ucfirst($request->template_type),
            'slug' => 'mi-portafolio-' . uniqid(),
            'description' => 'Mi portafolio profesional creado con PortafolioAI',
            'template_type' => $request->template_type,
            'template_data' => $this->getTemplateDefaultData($request->template_type),
            'is_public' => false,
            'is_active' => true,
            'is_completed' => false,
            'is_new' => true,
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
        if ($portfolio->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('Dashboard/Porfolios/Editor/Editor', [
            'portfolio' => $portfolio,
            'templateData' => $portfolio->template_data,
            'sections' => $portfolio->sections
        ]);
    }

    /**
 * Vista del portafolio terminado
 */
public function viewPortfolio(Portfolio $portfolio)
{
    // Verificar acceso: dueño o portafolio con acceso por enlace
    $isOwner = Auth::check() && $portfolio->user_id === Auth::id();
    
    // Si es modo owner_only y no es el dueño, denegar acceso
    if ($portfolio->access_mode === 'owner_only' && !$isOwner) {
        abort(404);
    }

    // URL pública del portafolio
    $publicUrl = $portfolio->access_mode === 'link' 
        ? url("/p/{$portfolio->slug}") 
        : null;

    // Puede editar: es dueño o tiene permiso view_edit
    $canEdit = $isOwner || $portfolio->link_permission === 'view_edit';

    return Inertia::render('Dashboard/Porfolios/View_Template/Final', [
        'portfolio' => $portfolio,
        'isOwner' => $isOwner,
        'publicUrl' => $publicUrl,
        'canEdit' => $canEdit,
    ]);
}
    /**
     * Vista pública por slug (lectura)
     */
    public function viewPublicBySlug(string $slug)
    {
        $portfolio = Portfolio::where('slug', $slug)->firstOrFail();

        return $this->viewPortfolio($portfolio);
    }

    /**
     * Editor público por slug (requiere permiso view_edit y autenticación)
     */
    public function editPublicBySlug(string $slug)
    {
        // 1. Verificar autenticación
        if (!Auth::check()) {
            // Guardar la URL intentada para redirigir después del login
            session()->put('url.intended', url("/p/{$slug}/edit"));
            
            return redirect()->route('login')
                ->with('warning', 'Debes iniciar sesión o registrarte para editar este portafolio.');
        }

        $portfolio = Portfolio::where('slug', $slug)->firstOrFail();

        // 2. Verificar que el portafolio tenga permiso de edición pública
        if ($portfolio->access_mode !== 'link' || $portfolio->link_permission !== 'view_edit') {
            return redirect()->route('portfolio.public.view', $slug)
                ->with('error', 'No tienes permiso para editar este portafolio.');
        }

        // Renderizar el editor con modo "público"
        return Inertia::render('Dashboard/Porfolios/Editor/Editor', [
            'portfolio' => $portfolio,
            'templateData' => $portfolio->template_data, // ✅ Datos necesarios para Editor.vue
            'sections' => $portfolio->sections,          // ✅ Secciones necesarias
            'isPublicEdit' => true, 
            'returnUrl' => url("/p/{$slug}"),
        ]);
    }

    /**
     * Guardar cambios desde editor público (requiere permiso view_edit y autenticación)
     */
    public function updatePublicBySlug(Request $request, string $slug)
    {
        // 1. Verificar autenticación
        if (!Auth::check()) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Debes iniciar sesión.'], 401);
            }
            return redirect()->route('login');
        }

        $portfolio = Portfolio::where('slug', $slug)->firstOrFail();

        // Verificar permiso
        if ($portfolio->access_mode !== 'link' || $portfolio->link_permission !== 'view_edit') {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'No tienes permiso para editar este portafolio.'], 403);
            }
            return redirect()->route('portfolio.public.view', $slug)
                ->with('error', 'No tienes permiso para editar este portafolio.');
        }

        // Validar y actualizar
        $validated = $request->validate([
            'template_data' => 'required|array',
        ]);

        $portfolio->update([
            'template_data' => $validated['template_data'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Portafolio actualizado correctamente.',
                'portfolio' => $portfolio->refresh(),
            ]);
        }

        return redirect()->route('portfolio.public.view', $slug)
            ->with('success', 'Portafolio actualizado correctamente.');
    }

    private function getTemplateDefaultData($templateType)
    {
        $defaultData = [
            'personal' => [
                'firstName' => Auth::user()->first_name,
                'lastName' => Auth::user()->last_name ?? '',
                'title' => '',
                'email' => Auth::user()->email,
                'phone' => '',
                'location' => '',
                'website' => '',
                'linkedin' => '',
                'github' => '',
                'summary' => '',
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
                
                break;
            case 'ejecutiva':
                $defaultData['personal']['title'] = 'Director Ejecutivo';
                
                break;
            case 'creativa':
                $defaultData['personal']['title'] = 'Diseñador Creativo';
                
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
        if ($portfolio->user_id !== Auth::id()) {
            abort(403);
        }

        $templateData = $request->template_data;
        $hasNewImages = false;

        // ✅ Procesar foto de perfil
        if (isset($templateData['personal']['photo']) && $templateData['personal']['photo']) {
            $originalPhoto = $templateData['personal']['photo'];
            $templateData['personal']['photo'] = $this->saveProfilePhoto(
                $templateData['personal']['photo'],
                $portfolio->id
            );
            // Si cambió (era base64 y ahora es ruta), marcar cambio
            if ($originalPhoto !== $templateData['personal']['photo']) {
                $hasNewImages = true;
            }
        }

        // ✅ Procesar fotos de proyectos
        if (isset($templateData['projects']) && is_array($templateData['projects'])) {
            $projectsBefore = $templateData['projects'];
            $templateData['projects'] = $this->saveProjectPhotos(
                $templateData['projects'],
                $portfolio->id
            );
        
            // 🔥 OPTIMIZACIÓN: Solo limpiar disco si hubo imágenes nuevas (base64 detectado)
            // Verificamos si algún proyecto tenía imagen en base64
            foreach ($projectsBefore as $p) {
                if (isset($p['image']) && strpos($p['image'], 'data:image') === 0) {
                    $hasNewImages = true;
                    break;
                }
            }

            if ($hasNewImages) {
                $this->cleanupOldProjectPhotos($portfolio->id, $templateData['projects']);
            }
        }
        
        // Actualizar el portfolio
        $dataToUpdate = [
            'template_data' => $templateData,
            'is_public' => $request->config['is_public'] ?? false,
            'title' => $request->config['title'] ?? $portfolio->title,
        ];
        
        if (isset($request->config['is_completed'])) {
            $dataToUpdate['is_completed'] = $request->config['is_completed'];
        }
        
        $portfolio->update($dataToUpdate);

        // 🔥 OPTIMIZACIÓN: Retornar JSON para evitar recarga de página lenta con Inertia
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Guardado correctamente',
                'portfolio' => $portfolio // Opcional, solo si el front lo necesita
            ]);
        }

        return back()->with('success', 'Portfolio actualizado correctamente');
    }

    /**
     * Actualizar solo el título del portafolio (para edición rápida desde Dashboard)
     */
    public function updateTitle(Request $request, Portfolio $portfolio)
    {
        if ($portfolio->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $portfolio->update([
            'title' => $request->title,
        ]);

        return back()->with('success', 'Título actualizado correctamente');
    }

    /**
     * Guardar foto de perfil en storage/portfolios/{id}/profile/
     */

    private function saveProfilePhoto($photoData, $portfolioId)
    {
        // Validar que no sea vacío o null
        if (!$photoData) {
            return null;
        }

        // Si ya es una ruta guardada, devolverla sin procesar
        if (is_string($photoData) && strpos($photoData, '/storage/') === 0) {
            return $photoData;
        }

        // Verificar si es base64
        if (!is_string($photoData) || strpos($photoData, 'data:image') !== 0) {
            return $photoData;  // ✅ CAMBIO: Retorna el dato como está, no null
        }

        try {
            // Extraer extensión
            preg_match('/data:image\/(\w+);base64,/', $photoData, $matches);
            $extension = $matches[1] ?? 'png';

            // Decodificar
            $imageData = substr($photoData, strpos($photoData, ',') + 1);
            $imageData = base64_decode($imageData, true);

            // Validar que la decodificación fue exitosa
            if ($imageData === false) {
                Log::error('Error decodificando imagen base64 para portfolio ' . $portfolioId);
                return $photoData;  // ✅ CAMBIO: Retorna el original en caso de error
            }

            // Crear ruta con carpeta del portfolio
            $filename = 'profile-' . time() . '.' . $extension;
            $folderPath = self::PORTFOLIO_STORAGE_PATH . '/' . $portfolioId . '/' . self::PROFILE_FOLDER;

            // Eliminar foto anterior
            $this->deleteOldProfilePhoto($portfolioId);

            // Guardar
            Storage::disk('public')->put($folderPath . '/' . $filename, $imageData);


            return '/storage/' . $folderPath . '/' . $filename;
        } catch (\Exception $e) {
            Log::error('Error guardando foto de perfil: ' . $e->getMessage());
            return $photoData;  // ✅ CAMBIO: Retorna el original en caso de excepción
        }
    }

    /**
     * Guardar fotos de proyectos en storage/portfolios/{id}/projects/
     * 
     * Procesa el array de proyectos y guarda las imágenes en base64
     */
    private function saveProjectPhotos($projects, $portfolioId)
    {
        return array_map(function ($project) use ($portfolioId) {
            // ✅ Verificar si existe el campo 'image'
            if (isset($project['image']) && $project['image']) {
                $imageData = $project['image'];

                // Si ya es una ruta guardada (/storage/...), no procesar
                if (strpos($imageData, '/storage/') === 0) {
                    return $project;
                }

                // Verificar si es base64
                if (strpos($imageData, 'data:image') === 0) {
                    // Extraer extensión
                    preg_match('/data:image\/(\w+);base64,/', $imageData, $matches);
                    $extension = $matches[1] ?? 'png';

                    // Decodificar base64
                    $decodedImage = substr($imageData, strpos($imageData, ',') + 1);
                    $decodedImage = base64_decode($decodedImage);

                    // Crear nombre único para el proyecto
                    $filename = 'project-' . ($project['id'] ?? time()) . '-' . time() . '.' . $extension;
                    $folderPath = self::PORTFOLIO_STORAGE_PATH . '/' . $portfolioId . '/' . self::PROJECTS_FOLDER;

                    // Guardar en storage
                    Storage::disk('public')->put($folderPath . '/' . $filename, $decodedImage);

                    // Actualizar la ruta en el proyecto
                    $project['image'] = '/storage/' . $folderPath . '/' . $filename;
                }
            }

            return $project;
        }, $projects);
    }

    /**
     * Eliminar foto de perfil antigua
     */
    private function deleteOldProfilePhoto($portfolioId)
    {
        $folderPath = self::PORTFOLIO_STORAGE_PATH . '/' . $portfolioId . '/' . self::PROFILE_FOLDER;

        if (Storage::disk('public')->exists($folderPath)) {
            $files = Storage::disk('public')->files($folderPath);
            foreach ($files as $file) {
                Storage::disk('public')->delete($file);
            }
        }
    }

    private function cleanupOldProjectPhotos($portfolioId, $updatedProjects)
{
    $folderPath = self::PORTFOLIO_STORAGE_PATH . '/' . $portfolioId . '/' . self::PROJECTS_FOLDER;

    if (!Storage::disk('public')->exists($folderPath)) {
        return;
    }

    // 1. Obtener archivos actuales
    $existingFiles = Storage::disk('public')->files($folderPath);

    // 2. Obtener rutas de imágenes que deben mantenerse
    $usedImages = collect($updatedProjects)
        ->pluck('image')
        ->filter(fn ($img) => is_string($img) && str_starts_with($img, '/storage/'))
        ->map(fn ($img) => str_replace('/storage/', '', $img)) // normalizar ruta
        ->toArray();

    // 3. Encontrar archivos no usados
    $filesToDelete = array_diff($existingFiles, $usedImages);

    // 4. Eliminar
    foreach ($filesToDelete as $file) {
        Storage::disk('public')->delete($file);
    }
}


    /**
     * Eliminar portfolio completo (limpia carpeta del storage)
     */
    public function deletePortfolio(Portfolio $portfolio)
    {
        if ($portfolio->user_id !== Auth::id()) {
            abort(403);
        }

        // Eliminar carpeta completa del portfolio
        $folderPath = self::PORTFOLIO_STORAGE_PATH . '/' . $portfolio->id;
        if (Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->deleteDirectory($folderPath);
        }

        $portfolio->delete();

        return back()->with('success', 'Portfolio eliminado correctamente');
    }
}
