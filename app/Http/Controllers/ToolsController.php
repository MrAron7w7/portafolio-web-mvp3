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
            /** @var \Illuminate\Http\Client\Response $response */
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
            'job_description' => 'nullable|string|max:5000',
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

        $jobDescriptionContext = "";
        if ($request->filled('job_description')) {
            $jobDescriptionContext = "\nDESCRIPCIÓN DE LA VACANTE:\n\"{$request->job_description}\"\n\nIMPORTANTE: Genera preguntas que evalúen específicamente si el candidato cumple con los requisitos de esta vacante basándose en su experiencia.\n";
        }

        $prompt = "Eres un experto en entrevistas de trabajo. Genera 6-8 preguntas de entrevista personalizadas para este candidato.{$jobDescriptionContext}

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
            /** @var \Illuminate\Http\Client\Response $response */
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
    public function interviewInteraction(Request $request)
    {
        $request->validate([
            'portfolio_id' => 'required|exists:portfolios,id',
            'job_description' => 'nullable|string|max:20000',
            'chat_history' => 'nullable|array',
            'message' => 'nullable|string'
        ]);

        $portfolio = Portfolio::where('user_id', Auth::id())
            ->where('id', $request->portfolio_id)
            ->first();

        if (!$portfolio) {
            return response()->json(['error' => 'No portfolio found'], 404);
        }

        // --- CONTEXT PREPARATION ---
        // --- CONTEXT PREPARATION ---
        $experience = collect($portfolio->template_data['experience'] ?? [])->map(function ($item) {
            return [
                'position' => $item['position'] ?? '',
                'company' => $item['company'] ?? '',
                'description' => substr($item['description'] ?? '', 0, 8000), // Increased text limit
                'start_date' => $item['start_date'] ?? '',
                'end_date' => $item['end_date'] ?? ''
            ];
        })->toArray();

        $projects = collect($portfolio->template_data['projects'] ?? [])->map(function ($item) {
            return [
                'name' => $item['name'] ?? $item['title'] ?? '',
                'description' => substr($item['description'] ?? '', 0, 8000),
                'technologies' => is_array($item['technologies'] ?? null) 
                    ? array_slice($item['technologies'], 0, 50) 
                    : substr($item['technologies'] ?? '', 0, 1000)
            ];
        })->toArray();

        // Filter skills to avoid sending huge lists if any
        $skills = $portfolio->template_data['skills'] ?? [];
        if (isset($skills['technical']) && is_array($skills['technical'])) {
             // simplify structure if it's complex
             $skills['technical'] = array_map(function($s) {
                 return is_array($s) ? ($s['name'] ?? '') : $s;
             }, array_slice($skills['technical'], 0, 50));
        }

        $profileData = json_encode([
            'title' => $portfolio->template_data['personal']['title'] ?? 'Profesional',
            'experience' => $experience,
            'projects' => $projects,
            'skills' => $skills
        ], JSON_UNESCAPED_UNICODE);

        $jobDescription = $request->job_description;
        $companyContext = $jobDescription 
            ? "LA EMPRESA:\n\"{$jobDescription}\"\n\nINSTRUCCIÓN CRÍTICA: TÚ ERES EL RECLUTADOR DE ESTA EMPRESA. Tu personalidad, preguntas y cultura deben reflejar exactamente la descripción anterior." 
            : "LA EMPRESA: Una Startup Tecnológica de alto crecimiento.";

        // --- SYSTEM PROMPT ---
        $systemPrompt = "ROL: Senior Recruiter.
{$companyContext}

OBJETIVO: Simular una primera entrevista real para esta empresa (nervios -> charla -> realidad -> feedback).

REGLAS DE INTERACCIÓN (ESTRICTAS):
1. INICIO (OBLIGATORIO): 
   - SI EL HISTORIAL DE CHAT ESTÁ VACÍO, DEBES SALUDAR PRIMERO.
   - Preséntate con nombre ficticio y cargo.
   - Menciona explícitamente a la empresa (según la descripción).
   - Haz una pregunta inicial amable para romper el hielo (ej: 'cuéntame sobre ti').

2. FLUJO:
   - Mantén el personaje TODO EL TIEMPO.
   - Sé profesional pero cercano.
   - Haz UNA pregunta a la vez. No abrumes.
   - Si la respuesta es breve/mala, indaga más.

3. CIERRE (Turno 6-8):
   - Avisa que es la última pregunta.
   - Despídete formalmente y da un breve feedback final sobre el desempeño simulado.

FORMATO: Texto plano (sin markdown). Conversacional.

DATOS DEL CANDIDATO:
{$profileData}
";

        // --- HISTORY BUILDING ---
        $fullPrompt = $systemPrompt . "\n\nHISTORIAL DE CONVERSACIÓN:\n";
        
        $history = $request->chat_history ?? [];
        
        // If user sent a new message, append it to history locally for the prompt
        if ($request->filled('message')) {
            $history[] = ['role' => 'user', 'content' => $request->message];
        }

        foreach ($history as $msg) {
            $role = $msg['role'] === 'user' ? 'Candidato' : 'Recuiter';
            $fullPrompt .= "{$role}: {$msg['content']}\n";
        }

        $fullPrompt .= "\nRecruiter (Tú):";

        // --- AI CALL ---
        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.stack_ai.key'),
                'Content-Type'  => 'application/json',
            ])->withoutVerifying()->post(
                'https://api.stack-ai.com/inference/v0/run/' . 
                config('services.stack_ai.flow_id') . '/' . 
                config('services.stack_ai.node_id'),
                [
                    'in-0'   => $fullPrompt,
                    'user_id'=> Auth::id()
                ]
            );

            if ($response->failed()) {
                \Illuminate\Support\Facades\Log::error('Stack AI Error: ' . $response->body());
                return response()->json(['error' => 'Error en la IA: ' . $response->status() . ' ' . $response->body()], 500);
            }

            $aiResponseData = $response->json();
            $aiText = data_get($aiResponseData, 'outputs.out-0', '');
            
            // Clean up if AI includes "Recruiter:" prefix
            $aiText = preg_replace('/^Recruiter \(Tú\):|^Recruiter:/i', '', trim($aiText));

            return response()->json([
                'reply' => $aiText,
                'history' => $history // Return updated history so frontend can maintain it
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Interview Interaction Error: ' . $e->getMessage());
            return response()->json(['error' => 'Error al conectar con el entrevistador: ' . $e->getMessage()], 500);
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

        // Filter and sanitize data to avoid huge payloads
        $experience = collect($portfolio->template_data['experience'] ?? [])->map(function ($item) {
            return [
                'position' => $item['position'] ?? '',
                'company' => $item['company'] ?? '',
                'description' => substr($item['description'] ?? '', 0, 5000),
                'start_date' => $item['start_date'] ?? '',
                'end_date' => $item['end_date'] ?? ''
            ];
        })->toArray();

        $projects = collect($portfolio->template_data['projects'] ?? [])->map(function ($item) {
            return [
                'name' => $item['name'] ?? $item['title'] ?? '',
                'description' => substr($item['description'] ?? '', 0, 5000),
                'technologies' => is_array($item['technologies'] ?? null) 
                    ? array_slice($item['technologies'], 0, 50) 
                    : substr($item['technologies'] ?? '', 0, 1000)
            ];
        })->toArray();

        // Filter skills
        $skills = $portfolio->template_data['skills'] ?? [];
        if (isset($skills['technical']) && is_array($skills['technical'])) {
             $skills['technical'] = array_map(function($s) {
                 return is_array($s) ? ($s['name'] ?? '') : $s;
             }, array_slice($skills['technical'], 0, 50));
        }

        $filteredData = [
            'title' => $portfolio->template_data['personal']['title'] ?? 'Profesional',
            'about' => substr($portfolio->template_data['personal']['about'] ?? '', 0, 5000),
            'experience' => $experience,
            'projects' => $projects,
            'skills' => $skills
        ];
        
        $portfolioText = json_encode($filteredData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $prompt = "ROL: Sistema Experto ATS (Applicant Tracking System).
TAREA: Analizar el siguiente portafolio y simular un reporte de compatibilidad.

PORTAFOLIO (JSON):
{$portfolioText}

INSTRUCCIÓN:
Genera un análisis en formato JSON estricto. NO expliques nada. Solo devuelve el JSON.

ESTRUCTURA DE RESPUESTA REQUERIDA:
{
    \"ats_score\": (número entero de 0 a 100 estimando probabilidad de éxito),
    \"keywords_found\": [\"lista\", \"de\", \"palabras\", \"clave\", \"detectadas\", \"en\", \"el\", \"perfil\"],
    \"keywords_missing\": [\"lista\", \"de\", \"palabras\", \"importantes\", \"que\", \"faltan\"],
    \"suggestions\": [\"3\", \"sugerencias\", \"tácticas\", \"para\", \"mejorar\", \"el\", \"score\"]
}";

        try {
            /** @var \Illuminate\Http\Client\Response $response */
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
            
            \Illuminate\Support\Facades\Log::info('ATS AI Input Prompt: ' . $prompt);
            \Illuminate\Support\Facades\Log::info('ATS AI Raw Output: ' . $analysisText);

            // Clean Markdown code blocks if present
            $analysisText = preg_replace('/^```json\s*|\s*```$/', '', trim($analysisText));
            $analysisText = preg_replace('/^```\s*|\s*```$/', '', $analysisText);
            
            $analysis = json_decode($analysisText, true);

            $defaults = [
                'ats_score' => 50,
                'keywords_found' => ['Analizando...'],
                'keywords_missing' => ['No se encontraron datos'],
                'suggestions' => ['Intenta escanear nuevamente']
            ];
            
            if (!$analysis) {
                \Illuminate\Support\Facades\Log::error('ATS JSON Decode Failed. Text: ' . $analysisText);
                // Fallback con análisis básico
                $analysis = [
                    'ats_score' => 65,
                    'keywords_found' => ['experiencia', 'proyectos'],
                    'keywords_missing' => ['resultados cuantificables', 'logros medibles'],
                    'suggestions' => [
                        'Agrega métricas cuantificables a tus logros',
                        'Incluye más palabras clave de tu industria'
                    ]
                ];
            }
            
            // Ensure all keys exist
            $analysis = array_merge($defaults, $analysis);
            
            // Force types
            $analysis['ats_score'] = (int) ($analysis['ats_score'] ?? 0);
            if (!is_array($analysis['keywords_found'])) $analysis['keywords_found'] = [];
            if (!is_array($analysis['keywords_missing'])) $analysis['keywords_missing'] = [];
            if (!is_array($analysis['suggestions'])) $analysis['suggestions'] = [];

            return response()->json($analysis);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al analizar ATS: ' . $e->getMessage()], 500);
        }
    }
}
