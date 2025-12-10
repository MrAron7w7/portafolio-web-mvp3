<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $portfolios = Portfolio::where('user_id', auth()->id())
            ->latest()
            ->get(['id', 'title', 'description', 'template_type', 'is_public', 'is_completed', 'created_at'])
            ->map(fn ($p) => [
                ...$p->only(['id', 'title', 'description', 'template_type', 'is_public', 'is_completed']),
                'createdAt' => $p->created_at->format('d M Y'),
                'status' => match(true) {
                    !$p->is_completed => 'borrador',
                    $p->is_public => 'publicado',
                    default => 'finalizado'
                },
                'statusColor' => match(true) {
                    !$p->is_completed => 'bg-gray-100 text-gray-800',
                    $p->is_public => 'bg-green-100 text-green-800',
                    default => 'bg-blue-100 text-blue-800'
                },
            ]);

        return Inertia::render('Dashboard/Dashboard', compact('portfolios'));
    }
}
