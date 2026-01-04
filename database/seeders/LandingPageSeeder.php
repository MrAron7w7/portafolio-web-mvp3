<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 0. Header Section (New)
        \App\Models\LandingPageSection::updateOrCreate(
            ['key' => 'header'],
            [
                'content' => [
                    'brand_name' => 'PortafolioAI',
                    'logo_text' => 'PortafolioAI', // Optional distinction
                ],
                'images' => []
            ]
        );
        
        // 0.5. Auth Section (New)
        \App\Models\LandingPageSection::updateOrCreate(
            ['key' => 'auth'],
            [
                'content' => [
                    'welcome_title' => 'Bienvenido a Portafolio Profesional IA',
                    'welcome_subtitle' => 'Crea tu portafolio perfecto con inteligencia artificial.',
                    'register_title' => 'Crea tu cuenta',
                    'register_subtitle' => 'Regístrate para crear tu portafolio profesional con IA.',
                ],
                'images' => [
                    'background' => null // Use null to verify fallback first
                ]
            ]
        );

        // 1. Hero Section
        \App\Models\LandingPageSection::updateOrCreate(
            ['key' => 'hero'],
            [
                'content' => [
                    'badge' => 'v2.0 Ahora con IA Avanzada',
                    'title_line1' => 'Potencia tu',
                    'title_gradient' => 'Marca Personal',
                    'description' => 'La plataforma todo en uno para construir tu portafolio, optimizar tu CV y preparar entrevistas. Diseñada para desarrolladores y creativos ambiciosos.',
                    'social_proof_text' => 'Uniendo a la comunidad tech',
                    'cv_score_label' => 'CV Score',
                    'cv_score_value' => '98/100',
                ],
                'images' => [
                    'main_image' => '/imagenes/Template.PNG',
                ]
            ]
        );

        // 2. Tools Section
        \App\Models\LandingPageSection::updateOrCreate(
            ['key' => 'tools'],
            [
                'content' => [
                    'header_subtitle' => 'Tu ecosistema de carrera',
                    'header_title' => 'Todo lo que necesitas en un solo lugar',
                    'items' => [
                        [
                            'icon' => 'Layout',
                            'title' => 'Plantillas Premium',
                            'description' => 'Diseños optimizados para conversión, adaptables a tu marca personal.',
                            'color' => 'from-blue-500 to-indigo-600'
                        ],
                        [
                            'icon' => 'FileText',
                            'title' => 'ATS Scanner',
                            'description' => 'Optimización de CV con IA para superar filtros de reclutamiento.',
                            'color' => 'from-purple-500 to-pink-600'
                        ],
                        [
                            'icon' => 'Mic',
                            'title' => 'Entrevistas IA',
                            'description' => 'Simulaciones realistas con feedback inmediato sobre tus respuestas.',
                            'color' => 'from-orange-400 to-red-500'
                        ],
                        [
                            'icon' => 'MessageSquare',
                            'title' => 'Comunidad',
                            'description' => 'Networking estratégico con profesionales de tu industria.',
                            'color' => 'from-emerald-400 to-teal-600'
                        ]
                    ]
                ],
                'images' => []
            ]
        );

        // 3. Features Section
        \App\Models\LandingPageSection::updateOrCreate(
            ['key' => 'features'],
            [
                'content' => [
                    'items' => [
                        [
                            'title' => 'Diseño Inteligente que Impacta',
                            'description' => 'Nuestra tecnología analiza tu perfil y genera un portafolio que destaca tus fortalezas clave. Sin arrastrar y soltar interminable, solo resultados profesionales al instante.',
                            'icon' => 'Zap',
                            'stats' => [
                                ['label' => 'Más rápido', 'value' => '10x'],
                                ['label' => 'Personalizable', 'value' => '100%']
                            ],
                            'image' => '/imagenes/AnalizaPerfil.PNG',
                            'imageGradient' => 'from-indigo-500/10 to-purple-500/10'
                        ],
                        [
                            'title' => 'Domina las Entrevistas Técnicas',
                            'description' => 'Practica con un entrenador de IA que se adapta a tu rol. Recibe consejos sobre claridad, tono y contenido técnico para llegar confiado a la entrevista real.',
                            'icon' => 'Mic',
                            'stats' => [
                                ['label' => 'Preguntas Reales', 'value' => '500+'],
                                ['label' => 'Feedback', 'value' => 'Instantáneo']
                            ],
                            'image' => null, // Placeholder in original
                            'imageGradient' => 'from-orange-500/10 to-red-500/10'
                        ]
                    ]
                ],
                'images' => []
            ]
        );

        // 3.5. Testimonials Section (New)
        \App\Models\LandingPageSection::updateOrCreate(
            ['key' => 'testimonials'],
            [
                'content' => [
                    'title' => 'Lo que dice la comunidad',
                    'subtitle' => 'Opiniones verificadas de profesionales destacados que ya consiguieron empleo.',
                    'items' => [
                        [
                            'name' => 'Carlos M.',
                            'role' => 'Full Stack Dev',
                            'content' => 'Gracias a PortafolioAI, conseguí mi primer trabajo remoto. El ATS Scanner es una herramienta increíble.',
                            'rating' => 5,
                            'avatar' => null
                        ],
                        [
                            'name' => 'Ana R.',
                            'role' => 'UX UI Designer',
                            'content' => 'Las plantillas son hermosas y muy fáciles de personalizar. Definitivaente elevó mi marca personal.',
                            'rating' => 5,
                            'avatar' => null
                        ],
                        [
                            'name' => 'David S.',
                            'role' => 'Software Engineer',
                            'content' => 'La simulación de entrevistas me dio la confianza que necesitaba. Totalmente recomendado.',
                            'rating' => 5,
                            'avatar' => null
                        ]
                    ]
                ],
                'images' => []
            ]
        );

        // 4. CTA Section
        \App\Models\LandingPageSection::updateOrCreate(
            ['key' => 'cta'],
            [
                'content' => [
                    'title' => 'Comienza a construir tu futuro hoy.',
                    'description' => 'Únete a miles de profesionales que ya están acelerando su carrera con nuestras herramientas de IA.',
                ],
                'images' => []
            ]
        );

        // 5. About Us Section (New)
        \App\Models\LandingPageSection::updateOrCreate(
            ['key' => 'about'],
            [
                'content' => [
                    'title' => 'Sobre Nosotros',
                    'who_we_are' => 'Somos un equipo de desarrolladores y diseñadores apasionados por la tecnología y el crecimiento profesional. Creemos que el talento no debe tener barreras, y por eso construimos herramientas que nivelan el campo de juego para todos.',
                    'mission_title' => 'Nuestra Misión',
                    'mission' => 'Democratizar el acceso al éxito profesional empoderando a desarrolladores y creativos con herramientas de Inteligencia Artificial de vanguardia. Nuestra misión es eliminar las barreras técnicas y de diseño en la búsqueda de empleo.',
                    'vision_title' => 'Nuestra Visión',
                    'vision' => 'Convertirnos en el ecosistema integral de carrera líder a nivel global, donde la tecnología y el talento humano convergen. Aspiramos a redefinir el futuro de la empleabilidad.',
                ],
                'images' => []
            ]
        );

        // 6. Footer Section
        \App\Models\LandingPageSection::updateOrCreate(
            ['key' => 'footer'],
            [
                'content' => [
                    'brand_description' => 'Empoderando a la próxima generación de profesionales con herramientas impulsadas por inteligencia artificial.',
                    'copyright_text' => 'PortafolioAI. Todos los derechos reservados.',
                    'brand_name' => 'PortafolioAI',
                ],
                'images' => []
            ]
        );
    }
}
