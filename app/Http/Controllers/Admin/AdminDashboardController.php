<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard principal de administración
     */
    public function index()
    {
        // Estadísticas globales
        $stats = [
            'total_users' => User::count(),
            'total_portfolios' => Portfolio::count(),
            'users_last_7_days' => User::where('created_at', '>=', Carbon::now()->subDays(7))->count(),
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

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
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
}
