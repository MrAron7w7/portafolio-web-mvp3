<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard principal de administración
     */
    public function index(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));

        // Estadísticas globales extendidas
        $stats = [
            'total_users' => User::count(),
            'total_portfolios' => Portfolio::count(),
            'users_last_7_days' => User::where('created_at', '>=', Carbon::now()->subDays(7))->count(),
            
            // Comunidad
            'total_posts' => \App\Models\CommunityPost::count(),
            'total_comments' => \App\Models\CommunityComment::count(),
            
            // Analíticas
            'total_views' => \App\Models\Analytic::sum('views'),
            'top_device' => \App\Models\Analytic::select('device_type', DB::raw('count(*) as total'))
                ->groupBy('device_type')
                ->orderByDesc('total')
                ->first()?->device_type ?? 'N/A',
                
            // Descargas
            'total_exports' => \App\Models\PortfolioExport::where('status', 'completed')->count(),
        ];

        // Plantilla más utilizada
        $mostUsedTemplate = Portfolio::select('template_type', DB::raw('count(*) as total'))
            ->groupBy('template_type')
            ->orderByDesc('total')
            ->first();

        $stats['most_used_template'] = $mostUsedTemplate?->template_type ?? 'Sin datos';

        // Usuarios recientes (últimos 7 días)
        $recentUsers = User::where('created_at', '>=', Carbon::now()->subDays(7))
            ->latest()
            ->take(10)
            ->get(['id', 'first_name', 'last_name', 'email', 'created_at'])
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'created_at' => $user->created_at->format('d M Y'),
                'days_ago' => $user->created_at->diffForHumans(),
            ]);

        // Datos del Gráfico (Dinámicos por año)
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            // Usamos createFromDate para asegurar el mes correcto sin desbordes por días
            $date = Carbon::createFromDate($year, $m, 1);
            
            $monthlyData[] = [
                'month' => $date->translatedFormat('M'), // Ene, Feb, etc.
                'users' => User::whereYear('created_at', $year)
                    ->whereMonth('created_at', $m)
                    ->count(),
                'portfolios' => Portfolio::whereYear('created_at', $year)
                    ->whereMonth('created_at', $m)
                    ->count(),
            ];
        }

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'chartData' => $monthlyData,
            'filters' => ['year' => $year]
        ]);
    }

    /**
     * Reportes del sistema
     */
    public function reports()
    {
        // Estadísticas mensuales (últimos 6 meses)
        $monthlyStats = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyStats[] = [
                'month' => $date->translatedFormat('M Y'),
                'users' => User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'portfolios' => Portfolio::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        // Estadísticas del mes actual
        $currentMonth = [
            'users' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'portfolios' => Portfolio::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // Por tipo de plantilla
        $templateStats = Portfolio::select('template_type', DB::raw('count(*) as total'))
            ->groupBy('template_type')
            ->orderByDesc('total')
            ->get();

        return Inertia::render('Admin/Reports', [
            'monthlyStats' => $monthlyStats,
            'currentMonth' => $currentMonth,
            'templateStats' => $templateStats,
        ]);
    }

    public function export(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $format = $request->input('format', 'csv');
        
        // Generar datos
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $date = Carbon::createFromDate($year, $m, 1);
            $users = User::whereYear('created_at', $year)->whereMonth('created_at', $m)->count();
            $portfolios = Portfolio::whereYear('created_at', $year)->whereMonth('created_at', $m)->count();
            
            $data[] = [
                'month' => $date->translatedFormat('F'),
                'users' => $users,
                'portfolios' => $portfolios
            ];
        }

        if ($format === 'pdf') {
            $html = view('admin.reports.pdf', [
                'year' => $year,
                'data' => $data,
                'totalUsers' => User::count(),
                'totalPortfolios' => Portfolio::count(),
                'generatedAt' => now()->format('d/m/Y H:i')
            ])->render();

            // Asegurar rutas correctas para Windows/Laragon
            $nodePath = config('services.browsershot.node_path', 'node');
            $npmPath = config('services.browsershot.npm_path', 'npm');

            $pdf = \Spatie\Browsershot\Browsershot::html($html)
                ->setNodeBinary($nodePath)
                ->setNpmBinary($npmPath)
                ->format('A4')
                ->showBackground()
                ->margins(20, 20, 20, 20)
                ->pdf();
                
            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="reporte-' . $year . '.pdf"');
        }

        if ($format === 'excel') {
            $fileName = 'reporte-actividad-' . $year . '.xls';
            $headers = array(
                "Content-Type"        => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );

            // Generar tabla HTML simple para Excel
            $html = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            $html .= '<table border="1">';
            $html .= '<thead><tr><th style="background-color:#f0f0f0;">Mes</th><th style="background-color:#f0f0f0;">Usuarios Registrados</th><th style="background-color:#f0f0f0;">Portafolios Creados</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($data as $row) {
                $html .= '<tr>';
                $html .= '<td>' . $row['month'] . '</td>';
                $html .= '<td>' . $row['users'] . '</td>';
                $html .= '<td>' . $row['portfolios'] . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';

            return response($html, 200, $headers);
        }

        // CSV fallback
        $fileName = 'reporte-actividad-' . $year . '.csv';
        
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Mes', 'Usuarios Registrados', 'Portafolios Creados');
        
        $callback = function() use($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($data as $row) {
                fputcsv($file, array($row['month'], $row['users'], $row['portfolios']));
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
