<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\View;

class PortfolioPdfController extends Controller
{
    public function download($id)
    {
        // 1. Buscar datos
        $portfolio = Portfolio::with(['user', 'sections', 'certificates'])->findOrFail($id);

        // 2. Renderizar la vista Blade a un String HTML
        $html = view('pdf.modern_layout', compact('portfolio'))->render();

        // 3. Generar el PDF
        // IMPORTANTE: Reemplaza la ruta de setNodeBinary con la que copiaste en el Paso 1
        $pdf = Browsershot::html($html)
            ->setNodeBinary(config('services.browsershot.node_path')) //Se cambia en el .env
            ->setNpmBinary(config('services.browsershot.npm_path')) //Se cambia en el .env
            ->format('A4')
            ->margins(10, 10, 10, 10)
            ->showBackground() // Para ver colores de fondo e imágenes
            ->waitUntilNetworkIdle() // Espera a que carguen imágenes externas (CDN, fotos)
            ->pdf();

        // 4. Devolver descarga
        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="portfolio.pdf"');
    }
}