<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\View;

class PortfolioPdfController extends Controller
{
    public function download($id)
    {
        // 1. Buscamos el portafolio
        $portfolio = Portfolio::findOrFail($id);

        // 2. Determinamos qué vista Blade usar según el tipo de template
        // Ejemplo: si template_type es 'moderna', buscará 'pdf.moderna'
        $viewName = 'pdf.' . $portfolio->template_type;

        // Fallback: Si no has creado la vista todavía, usa una por defecto para que no falle
        if (!View::exists($viewName)) {
            $viewName = 'pdf.moderna'; // Asegúrate de crear al menos esta primero
        }

        // 3. Renderizar HTML
        // Pasamos $data directamente para no escribir $portfolio->template_data['personal']... a cada rato
        $data = $portfolio->template_data;
        
        $html = view($viewName, compact('portfolio', 'data'))->render();

        // 4. Generar PDF con Browsershot
        $pdf = Browsershot::html($html)
            ->setNodeBinary(config('services.browsershot.node_path')) 
            ->setNpmBinary(config('services.browsershot.npm_path'))
            ->format('A4')
            ->margins(10, 10, 10, 10)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->pdf();

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="portafolio-'.$portfolio->slug.'.pdf"');
    }
}