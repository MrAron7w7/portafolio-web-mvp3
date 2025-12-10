<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class PortfolioPdfController extends Controller
{
    public function renderForPdf($id)
    {
        $portfolio = Portfolio::findOrFail($id);

        return Inertia::render('Print/PortfolioPrint', [
            'portfolio' => $portfolio
        ]);
    }

    public function download($id)
    {
        $portfolio = Portfolio::findOrFail($id);

        if (env('APP_URL_PDF')) {
            URL::forceRootUrl(env('APP_URL_PDF'));
        }

        $targetUrl = URL::temporarySignedRoute(
            'portfolio.print.view',
            now()->addMinutes(2),
            ['id' => $id]
        );

        if (env('APP_URL_PDF')) {
            URL::forceRootUrl(null);
        }

        // Primero crea un screenshot temporal para obtener dimensiones
        $tempBrowsershot = Browsershot::url($targetUrl)
            ->setNodeBinary(config('services.browsershot.node_path'))
            ->setNpmBinary(config('services.browsershot.npm_path'))
            ->windowSize(1200, 800)
            ->waitUntilNetworkIdle();

        // Obtén las dimensiones usando bodyHtml como truco
        $bodyScript = <<<'JS'
            (() => {
                const width = Math.max(
                    document.documentElement.scrollWidth,
                    document.body.scrollWidth
                );
                const height = Math.max(
                    document.documentElement.scrollHeight,
                    document.body.scrollHeight
                );
                return JSON.stringify({width, height});
            })();
        JS;

        try {
            $dimensionsJson = $tempBrowsershot->evaluate($bodyScript);
            $dimensions = json_decode($dimensionsJson, true);
        } catch (\Exception $e) {
            // Si falla, usa dimensiones por defecto
            $dimensions = ['width' => 1200, 'height' => 3000];
        }

        // Convierte píxeles a milímetros (96 DPI)
        $widthMm = max(317, ceil($dimensions['width'] * 0.2645833333)); // Mínimo A4 ancho
        $heightMm = max(500, ceil($dimensions['height'] * 0.2645833333)); // Mínimo 500mm

        // Genera el PDF con las dimensiones personalizadas
        $pdf = Browsershot::url($targetUrl)
            ->setNodeBinary(config('services.browsershot.node_path'))
            ->setNpmBinary(config('services.browsershot.npm_path'))
            ->paperSize($widthMm, $heightMm, 'mm')
            ->margins(0, 0, 0, 0)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->emulateMediaType('screen')
            ->pdf();

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="portafolio-' . $portfolio->slug . '.pdf"');
    }
}