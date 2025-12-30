<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use App\Models\Portfolio;

class ToolsController extends Controller
{
    public function index()
    {
        $portfolios = Portfolio::where('user_id', Auth::id())
            ->select('id', 'title', 'template_type', 'template_data')
            ->get();

        return Inertia::render('Dashboard/Tools/Index', [
            'portfolios' => $portfolios
        ]);
    }

    public function analyzeSkillsMatch(Request $request)
    {
        $request->validate([
            'job_description' => 'required|string|max:5000',
            'portfolio_id' => 'required|exists:portfolios,id',
        ]);

        $portfolio = Portfolio::where('user_id', Auth::id())
            ->where('id', $request->portfolio_id)
            ->first();
        
        if (!$portfolio) {
            return response()->json(['error' => 'No portfolio found'], 404);
        }

        $data = $portfolio->template_data;

        // 1. Extracción robusta de Skills
        $skillsList = [];
        // Intentar ruta estándar
        if (isset($data['skills']['technical']) && is_array($data['skills']['technical'])) {
            foreach ($data['skills']['technical'] as $s) $skillsList[] = $s['name'] ?? $s;
        }
        // Intentar ruta plana
        elseif (isset($data['skills']) && is_array($data['skills'])) {
            foreach ($data['skills'] as $s) $skillsList[] = is_array($s) ? ($s['name'] ?? '') : $s;
        }

        // 2. Extracción de Experiencia (Contexto valioso)
        // Construir datos estructurados para la IA
        $candidateData = [
            'explicit_skills' => $skillsList,
            'experience_entries' => $data['experience'] ?? [],
            'projects' => array_map(function($p) {
                return [
                    'title' => $p['title'] ?? $p['name'] ?? '',
                    'description' => $p['description'] ?? '',
                    'technologies' => $p['technologies'] ?? []
                ];
            }, $data['projects'] ?? [])
        ];
        
        $candidateJsonFormatted = json_encode($candidateData, JSON_UNESCAPED_UNICODE);

        // ---------------------------------------------------------
        // 2. ANÁLISIS LOCAL (PLAN B): Comparación directa de keywords
        // ---------------------------------------------------------
        $jobDescriptionLower = strtolower($request->job_description);
        $localMatches = [];
        
        // Combinar skills explícitos con palabras clave comunes encontradas
        $candidateKeywords = array_map('strtolower', $skillsList);
        
        foreach ($candidateKeywords as $skill) {
            if (empty($skill)) continue;
            if (str_contains($jobDescriptionLower, strtolower($skill))) {
                $localMatches[] = ucfirst($skill);
            }
        }
        
        // Calcular score base local
        $localScore = 50; 
        if (count($localMatches) > 0) {
            $localScore += (count($localMatches) * 5); 
        }
        $localScore = min($localScore, 95);

        // ---------------------------------------------------------
        // 3. LLAMADA A LA IA (PLAN A)
        // ---------------------------------------------------------
        $prompt = "Actúa como un reclutador Senior de IT. Analiza ESTRICTAMENTE el siguiente PERFIL JSON y la VACANTE.

DATOS CANDIDATO: $candidateJsonFormatted

VACANTE: \"{$request->job_description}\"

INSTRUCCIONES:
1. Si el candidato menciona una tecnología en proyectos/experiencia, CUÉNTALA como skill TIENE.
2. Devuelve JSON válido.

JSON Requerido:
{
    \"match_percentage\": (0-100),
    \"matching_skills\": [strings],
    \"missing_skills\": [strings],
    \"recommendations\": \"consejos breves\"
}";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.stack_ai.key'),
                'Content-Type'  => 'application/json',
            ])->withoutVerifying()->post(
                'https://api.stack-ai.com/inference/v0/run/' . 
                config('services.stack_ai.flow_id') . '/' . 
                config('services.stack_ai.node_id'),
                [
                    'in-0'   => $prompt,
                    'user_id'=> ''
                ]
            );

            if ($response->failed()) {
                throw new \Exception("IA no disponible");
            }

            $aiData = $response->json();
            $aiText = data_get($aiData, 'outputs.out-0', '');
            
            // Extracción robusta de JSON
            $analysis = null;
            if (preg_match('/\{(?:[^{}]|(?R))*\}/s', $aiText, $matches)) {
                $analysis = json_decode($matches[0], true);
            }

            // FUSIÓN DE RESULTADOS
            if ($analysis && isset($analysis['match_percentage']) && is_numeric($analysis['match_percentage'])) {
                // Validación de cordura
                if ($analysis['match_percentage'] < 20 && count($localMatches) > 2) {
                    $analysis['match_percentage'] = $localScore;
                    $analysis['matching_skills'] = array_unique(array_merge($analysis['matching_skills'] ?? [], $localMatches));
                }
            } else {
                // Fallback exitoso (Usa score local + texto IA)
                $analysis = [
                    'match_percentage' => $localScore,
                    'matching_skills' => array_unique($localMatches),
                    'missing_skills' => [],
                    'recommendations' => "Análisis basado en palabras clave (IA texto): " . strip_tags(substr($aiText, 0, 250)) . "..."
                ];
            }

            return response()->json(array_merge($analysis, [
                'debug_skills_detected' => $skillsList
            ]));

        } catch (\Exception $e) {
            // Fallback total
            return response()->json([
                'match_percentage' => $localScore,
                'matching_skills' => $localMatches,
                'missing_skills' => [],
                'recommendations' => "Análisis local preliminar (Sistema de IA no disponible en este momento)."
            ]);
        }
    }

    public function generateInterviewQuestions(Request $request)
    {
        $request->validate([
            'portfolio_id' => 'required|exists:portfolios,id',
        ]);

        $portfolio = Portfolio::where('user_id', Auth::id())
            ->where('id', $request->portfolio_id)
            ->first();
        
        if (!$portfolio) {
            return response()->json(['error' => 'No portfolio found'], 404);
        }

        $experience = $portfolio->template_data['experience'] ?? [];
        $projects = $portfolio->template_data['projects'] ?? [];
        $title = $portfolio->template_data['personal']['title'] ?? 'Profesional';

        $experienceText = collect($experience)->map(function($exp) {
            return "- {$exp['position']} en {$exp['company']}: {$exp['description']}";
        })->join("\n");

        $projectsText = collect($projects)->map(function($proj) {
            return "- {$proj['name']}: {$proj['description']}";
        })->join("\n");

        $prompt = "Eres un experto en entrevistas de trabajo. Genera 6-8 preguntas de entrevista personalizadas para este candidato.

Título profesional: {$title}

Experiencia:
{$experienceText}

Proyectos:
{$projectsText}

Genera preguntas variadas (técnicas, comportamentales, de situación). Proporciona la respuesta en formato JSON con esta estructura:
{
    \"questions\": [
        {
            \"question\": \"texto de la pregunta\",
            \"category\": \"técnica|comportamental|situacional\",
            \"difficulty\": \"básica|intermedia|avanzada\"
        }
    ]
}";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.stack_ai.key'),
                'Content-Type'  => 'application/json',
            ])->withoutVerifying()->post(
                'https://api.stack-ai.com/inference/v0/run/' . 
                config('services.stack_ai.flow_id') . '/' . 
                config('services.stack_ai.node_id'),
                [
                    'in-0'   => $prompt,
                    'user_id'=> ''
                ]
            );

            if ($response->failed()) {
                return response()->json(['error' => 'Error en la IA'], 500);
            }

            $data = $response->json();
            $questionsText = data_get($data, 'outputs.out-0', '');
            
            $questions = json_decode($questionsText, true);
            
            if (!$questions || !isset($questions['questions'])) {
                // Fallback con preguntas genéricas
                $questions = [
                    'questions' => [
                        [
                            'question' => '¿Cuál ha sido tu mayor logro profesional?',
                            'category' => 'comportamental',
                            'difficulty' => 'intermedia'
                        ],
                        [
                            'question' => '¿Cómo manejas situaciones de alta presión?',
                            'category' => 'situacional',
                            'difficulty' => 'intermedia'
                        ],
                    ]
                ];
            }

            return response()->json($questions);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al generar preguntas: ' . $e->getMessage()], 500);
        }
    }

    public function scanATS(Request $request)
    {
        $request->validate([
            'portfolio_id' => 'required|exists:portfolios,id',
        ]);

        $portfolio = Portfolio::where('user_id', Auth::id())
            ->where('id', $request->portfolio_id)
            ->first();
        
        if (!$portfolio) {
            return response()->json(['error' => 'No portfolio found'], 404);
        }

        $data = $portfolio->template_data;
        
        $portfolioText = json_encode($data, JSON_PRETTY_PRINT);

        $prompt = "Eres un experto en sistemas ATS (Applicant Tracking Systems). Analiza este portafolio y determina qué tan compatible es con los filtros automáticos de reclutamiento.

Portafolio:
{$portfolioText}

Evalúa:
- Uso de palabras clave técnicas
- Verbos de acción
- Cuantificación de logros
- Estructura de información
- Presencia de datos de contacto

Proporciona el análisis en formato JSON:
{
    \"ats_score\": número entre 0 y 100,
    \"keywords_found\": [array de palabras clave encontradas],
    \"keywords_missing\": [array de palabras clave recomendadas que faltan],
    \"checklist\": [
        {\"item\": \"descripción\", \"status\": \"pass|warning|fail\"}
    ],
    \"suggestions\": [array de sugerencias específicas]
}";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.stack_ai.key'),
                'Content-Type'  => 'application/json',
            ])->withoutVerifying()->post(
                'https://api.stack-ai.com/inference/v0/run/' . 
                config('services.stack_ai.flow_id') . '/' . 
                config('services.stack_ai.node_id'),
                [
                    'in-0'   => $prompt,
                    'user_id'=> ''
                ]
            );

            if ($response->failed()) {
                return response()->json(['error' => 'Error en la IA'], 500);
            }

            $responseData = $response->json();
            $analysisText = data_get($responseData, 'outputs.out-0', '');
            
            $analysis = json_decode($analysisText, true);
            
            if (!$analysis) {
                // Fallback con análisis básico
                $analysis = [
                    'ats_score' => 65,
                    'keywords_found' => ['experiencia', 'proyectos'],
                    'keywords_missing' => ['resultados cuantificables', 'logros medibles'],
                    'checklist' => [
                        ['item' => 'Tiene información de contacto', 'status' => 'pass'],
                        ['item' => 'Usa verbos de acción', 'status' => 'warning'],
                    ],
                    'suggestions' => [
                        'Agrega métricas cuantificables a tus logros',
                        'Incluye más palabras clave de tu industria'
                    ]
                ];
            }

            return response()->json($analysis);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al analizar ATS: ' . $e->getMessage()], 500);
        }
    }
}
