<?php
// app/Services/TemplateService.php

namespace App\Http\Services;

class TemplateService
{
    const TEMPLATES = [
        1 => [
            'id' => 1,
            'name' => 'Moderna',
            'description' => 'Diseño contemporáneo con secciones dinámicas y animaciones sutiles',
            'category' => 'Profesional',
            'component' => 'ModernTemplate',
            'color' => 'from-blue-500 to-cyan-500',
            'sections' => ['hero', 'about', 'skills', 'experience', 'projects', 'contact'],
            'features' => ['Responsive', 'Animaciones', 'Secciones modulares']
        ],
        2 => [
            'id' => 2,
            'name' => 'Minimalista',
            'description' => 'Enfoque limpio y elegante con máximo impacto visual',
            'category' => 'Diseño',
            'component' => 'MinimalTemplate',
            'color' => 'from-gray-600 to-gray-800',
            'sections' => ['hero', 'about', 'portfolio', 'contact'],
            'features' => ['Limpio', 'Enfoque en contenido', 'Tipografía elegante']
        ],
        3 => [
            'id' => 3,
            'name' => 'Ejecutiva',
            'description' => 'Profesional y corporativo, ideal para negocios y consultoría',
            'category' => 'Corporativo',
            'component' => 'ExecutiveTemplate',
            'color' => 'from-green-500 to-emerald-600',
            'sections' => ['hero', 'about', 'services', 'experience', 'testimonials', 'contact'],
            'features' => ['Corporativo', 'Estructurado', 'Profesional']
        ],
        4 => [
            'id' => 4,
            'name' => 'Creativa',
            'description' => 'Perfecta para diseñadores, artistas y perfiles creativos',
            'category' => 'Portafolio',
            'component' => 'CreativeTemplate',
            'color' => 'from-purple-500 to-pink-500',
            'sections' => ['hero', 'gallery', 'about', 'projects', 'skills', 'contact'],
            'features' => ['Visual', 'Interactivo', 'Portfolio focus']
        ],
        5 => [
            'id' => 5,
            'name' => 'Tecnológica',
            'description' => 'Diseño tech-forward para desarrolladores y profesionales IT',
            'category' => 'Tech',
            'component' => 'TechTemplate',
            'color' => 'from-orange-500 to-red-500',
            'sections' => ['hero', 'skills', 'projects', 'experience', 'github', 'contact'],
            'features' => ['Code-friendly', 'Tecnológico', 'Moderno']
        ]
    ];

    public static function getTemplates()
    {
        return collect(self::TEMPLATES);
    }

    public static function getTemplate($id)
    {
        return self::TEMPLATES[$id] ?? null;
    }

    public static function getDefaultTemplateData($templateId)
    {
        $template = self::getTemplate($templateId);
        
        if (!$template) {
            return [];
        }

        return [
            'template_type' => $templateId,
            'sections' => collect($template['sections'])->map(function($section) {
                return [
                    'type' => $section,
                    'content' => [],
                    'visible' => true,
                    'order' => 0
                ];
            })->toArray(),
            'theme_settings' => [
                'primary_color' => '#005aeb',
                'font_family' => 'Inter',
                'dark_mode' => false
            ]
        ];
    }
}