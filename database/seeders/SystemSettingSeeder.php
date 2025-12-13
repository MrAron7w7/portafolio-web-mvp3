<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ========== GENERAL & BRANDING ==========
            ['key' => 'app_name', 'value' => 'PortafolioAI', 'group' => 'general', 'type' => 'text'],
            ['key' => 'app_logo', 'value' => '', 'group' => 'general', 'type' => 'image'],
            
            // ========== COLORES ==========
            ['key' => 'primary_color', 'value' => '#005aeb', 'group' => 'appearance', 'type' => 'color'],
            ['key' => 'secondary_color', 'value' => '#7B2FF7', 'group' => 'appearance', 'type' => 'color'],

            // ========== HERO SECTION ==========
            ['key' => 'hero_title_1', 'value' => 'Crea tu Portafolio', 'group' => 'home_hero', 'type' => 'text'],
            ['key' => 'hero_title_2', 'value' => 'Profesional en', 'group' => 'home_hero', 'type' => 'text'],
            ['key' => 'hero_title_highlight', 'value' => 'Minutos con IA', 'group' => 'home_hero', 'type' => 'text'],
            ['key' => 'hero_description', 'value' => 'Genera un portafolio moderno, personalizable y listo para compartir, sin conocimientos de diseño. Destaca entre la multitud con ayuda de inteligencia artificial.', 'group' => 'home_hero', 'type' => 'textarea'],
            ['key' => 'hero_cta_main', 'value' => 'Crear mi Portafolio Ahora', 'group' => 'home_hero', 'type' => 'text'],
            ['key' => 'hero_cta_secondary', 'value' => 'Ver Ejemplos', 'group' => 'home_hero', 'type' => 'text'],
            
            // ========== ESTADÍSTICAS (Stats) ==========
            ['key' => 'stats_users_count', 'value' => '15000', 'group' => 'home_stats', 'type' => 'number'],
            ['key' => 'stats_users_label', 'value' => 'Usuarios Activos', 'group' => 'home_stats', 'type' => 'text'],
            ['key' => 'stats_portfolios_count', 'value' => '50000', 'group' => 'home_stats', 'type' => 'number'],
            ['key' => 'stats_portfolios_label', 'value' => 'Portafolios Creados', 'group' => 'home_stats', 'type' => 'text'],
            ['key' => 'stats_satisfaction_count', 'value' => '98', 'group' => 'home_stats', 'type' => 'number'],
            ['key' => 'stats_satisfaction_label', 'value' => 'Satisfacción', 'group' => 'home_stats', 'type' => 'text'],

            // ========== SECCIÓN "POR QUÉ ELEGIR" ==========
            ['key' => 'features_title', 'value' => 'Por qué elegir PortafolioAI', 'group' => 'home_features', 'type' => 'text'],
            ['key' => 'features_subtitle', 'value' => 'Combinamos la potencia de la inteligencia artificial con diseño profesional para crear tu portafolio perfecto.', 'group' => 'home_features', 'type' => 'textarea'],
            
            // Feature 1
            ['key' => 'feature_1_icon', 'value' => '🤖', 'group' => 'home_features', 'type' => 'text'],
            ['key' => 'feature_1_title', 'value' => 'Diseño Automático con IA', 'group' => 'home_features', 'type' => 'text'],
            ['key' => 'feature_1_description', 'value' => 'Nuestra inteligencia artificial crea diseños únicos y profesionales basados en tu perfil.', 'group' => 'home_features', 'type' => 'textarea'],
            ['key' => 'feature_1_stat', 'value' => '2min', 'group' => 'home_features', 'type' => 'text'],
            ['key' => 'feature_1_stat_label', 'value' => 'Creación promedio', 'group' => 'home_features', 'type' => 'text'],
            
            // Feature 2
            ['key' => 'feature_2_icon', 'value' => '🎨', 'group' => 'home_features', 'type' => 'text'],
            ['key' => 'feature_2_title', 'value' => 'Plantillas Profesionales', 'group' => 'home_features', 'type' => 'text'],
            ['key' => 'feature_2_description', 'value' => 'Elige entre múltiples plantillas modernas y adaptables a cualquier industria.', 'group' => 'home_features', 'type' => 'textarea'],
            ['key' => 'feature_2_stat', 'value' => '50+', 'group' => 'home_features', 'type' => 'text'],
            ['key' => 'feature_2_stat_label', 'value' => 'Plantillas disponibles', 'group' => 'home_features', 'type' => 'text'],
            
            // Feature 3
            ['key' => 'feature_3_icon', 'value' => '🚀', 'group' => 'home_features', 'type' => 'text'],
            ['key' => 'feature_3_title', 'value' => 'Exportación y Hosting', 'group' => 'home_features', 'type' => 'text'],
            ['key' => 'feature_3_description', 'value' => 'Publica tu portafolio al instante con hosting incluido y dominio personalizable.', 'group' => 'home_features', 'type' => 'textarea'],
            ['key' => 'feature_3_stat', 'value' => '99.9%', 'group' => 'home_features', 'type' => 'text'],
            ['key' => 'feature_3_stat_label', 'value' => 'Uptime garantizado', 'group' => 'home_features', 'type' => 'text'],
            
            // Feature 4
            ['key' => 'feature_4_icon', 'value' => '⚙️', 'group' => 'home_features', 'type' => 'text'],
            ['key' => 'feature_4_title', 'value' => 'Personalización Total', 'group' => 'home_features', 'type' => 'text'],
            ['key' => 'feature_4_description', 'value' => 'Ajusta colores, fuentes y disposición para que refleje tu estilo personal.', 'group' => 'home_features', 'type' => 'textarea'],
            ['key' => 'feature_4_stat', 'value' => '∞', 'group' => 'home_features', 'type' => 'text'],
            ['key' => 'feature_4_stat_label', 'value' => 'Posibilidades', 'group' => 'home_features', 'type' => 'text'],

            // ========== SECCIÓN "CÓMO FUNCIONA" ==========
            ['key' => 'how_it_works_title', 'value' => 'Cómo funciona', 'group' => 'home_how', 'type' => 'text'],
            ['key' => 'how_it_works_subtitle', 'value' => 'Crea tu portafolio profesional en solo 3 sencillos pasos', 'group' => 'home_how', 'type' => 'text'],
            
            ['key' => 'step_1_title', 'value' => 'Cuéntanos sobre ti', 'group' => 'home_how', 'type' => 'text'],
            ['key' => 'step_1_description', 'value' => 'Completa tu información profesional, experiencia y proyectos destacados.', 'group' => 'home_how', 'type' => 'text'],
            
            ['key' => 'step_2_title', 'value' => 'IA genera tu portafolio', 'group' => 'home_how', 'type' => 'text'],
            ['key' => 'step_2_description', 'value' => 'Nuestra inteligencia artificial diseña automáticamente tu portafolio perfecto.', 'group' => 'home_how', 'type' => 'text'],
            
            ['key' => 'step_3_title', 'value' => 'Publica y comparte', 'group' => 'home_how', 'type' => 'text'],
            ['key' => 'step_3_description', 'value' => 'Comparte tu portafolio profesional con el mundo en minutos.', 'group' => 'home_how', 'type' => 'text'],

            // ========== TESTIMONIOS ==========
            ['key' => 'testimonials_title', 'value' => 'Lo que dicen nuestros usuarios', 'group' => 'home_testimonials', 'type' => 'text'],
            ['key' => 'testimonials_subtitle', 'value' => 'Miles de profesionales ya han transformado su presencia digital con PortafolioAI', 'group' => 'home_testimonials', 'type' => 'text'],

            // ========== PLANES ==========
            ['key' => 'pricing_title', 'value' => 'Elige tu plan perfecto', 'group' => 'home_pricing', 'type' => 'text'],
            ['key' => 'pricing_subtitle', 'value' => 'Desde un portafolio básico hasta soluciones empresariales completas', 'group' => 'home_pricing', 'type' => 'text'],

            // ========== CTA FINAL ==========
            ['key' => 'cta_title', 'value' => 'Empieza a construir tu marca personal hoy', 'group' => 'home_cta', 'type' => 'text'],
            ['key' => 'cta_subtitle', 'value' => 'Únete a miles de profesionales que ya están mostrando su trabajo al mundo', 'group' => 'home_cta', 'type' => 'text'],
            ['key' => 'cta_button', 'value' => 'Crear Portafolio con IA', 'group' => 'home_cta', 'type' => 'text'],

            // ========== FOOTER ==========
            ['key' => 'footer_description', 'value' => 'Creamos portafolios profesionales con inteligencia artificial para que destaquen tu talento y consigas las mejores oportunidades.', 'group' => 'footer', 'type' => 'textarea'],
            ['key' => 'footer_email', 'value' => 'hola@portafolioai.com', 'group' => 'footer', 'type' => 'text'],
            ['key' => 'footer_phone', 'value' => '+1 (555) 123-4567', 'group' => 'footer', 'type' => 'text'],
            ['key' => 'footer_location', 'value' => 'Ciudad, País', 'group' => 'footer', 'type' => 'text'],
            ['key' => 'footer_copyright', 'value' => '2024 PortafolioAI. Todos los derechos reservados.', 'group' => 'footer', 'type' => 'text'],
            
            // Redes sociales
            ['key' => 'social_facebook', 'value' => '#', 'group' => 'footer', 'type' => 'text'],
            ['key' => 'social_twitter', 'value' => '#', 'group' => 'footer', 'type' => 'text'],
            ['key' => 'social_instagram', 'value' => '#', 'group' => 'footer', 'type' => 'text'],
            ['key' => 'social_linkedin', 'value' => '#', 'group' => 'footer', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']], 
                $setting
            );
        }
    }
}