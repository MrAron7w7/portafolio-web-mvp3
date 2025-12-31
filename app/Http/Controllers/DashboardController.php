<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\CommunityPost;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Get portfolio IDs that have been published to community
        $publishedPortfolioIds = CommunityPost::where('user_id', Auth::id())
            ->pluck('portfolio_id')
            ->toArray();

        $portfolios = Portfolio::where('user_id', Auth::id())
            ->latest()
            ->get(['id', 'title', 'description', 'template_type', 'template_data', 'is_public', 'is_completed', 'created_at'])
            ->map(fn ($p) => [
                ...$p->only(['id', 'title', 'description', 'template_type', 'template_data', 'is_public', 'is_completed']),
                'createdAt' => $p->created_at->format('d M Y'),
                'is_published_in_community' => in_array($p->id, $publishedPortfolioIds),
                'status' => match(true) {
                    !$p->is_completed => 'borrador',
                    $p->is_public => 'publicado',
                    default => 'finalizado'
                },
                'statusColor' => match(true) {
                    !$p->is_completed => 'bg-slate-100 text-slate-800',
                    $p->is_public => 'bg-emerald-100 text-emerald-800',
                    default => 'bg-pink-100 text-pink-800'
                },
            ]);

        return Inertia::render('Dashboard/Dashboard', compact('portfolios'));
    }
}
