<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\CommunityPost;
use App\Models\CommunityPostRating;
use App\Models\Portfolio;
use Illuminate\Support\Facades\Auth;

class CommunityController extends Controller
{
    /**
     * Página pública de comunidad (landing)
     */
    public function index()
    {
        $posts = CommunityPost::with([
                'user:id,first_name,last_name,profile_photo_path',
                'portfolio:id,title,slug,theme_settings',
                'comments' => function ($query) {
                    $query->latest()
                        ->take(2)
                        ->with('user:id,first_name,last_name,profile_photo_path');
                }
            ])
            ->withCount(['comments', 'ratings'])
            ->withAvg('ratings', 'rating')
            ->orderByDesc('ratings_avg_rating')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Community/PublicIndex', [
            'posts' => $posts,
            'isAuthenticated' => Auth::check(),
        ]);
    }

    /**
     * Dashboard Community Section (Feed of Posts)
     */
    public function dashboardIndex()
    {
        $posts = CommunityPost::with(['user:id,first_name,last_name,profile_photo_path', 'portfolio:id,title,slug,user_id,theme_settings'])
            ->withCount(['comments', 'ratings'])
            ->withAvg('ratings', 'rating')
            ->orderByDesc('ratings_avg_rating')
            ->orderByDesc('created_at')
            ->paginate(12);

        // Get user's eligible portfolios for posting (not already posted)
        $userPortfolios = Portfolio::where('user_id', Auth::id())
            ->whereDoesntHave('communityPost')
            ->select('id', 'title')
            ->get();

        return Inertia::render('Dashboard/Community/Index', [
            'posts' => $posts,
            'eligiblePortfolios' => $userPortfolios
        ]);
    }

    /**
     * Store a new Community Post
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'portfolio_id' => 'required|exists:portfolios,id',
            'content' => 'required|string|max:5000',
        ]);

        // Verify ownership
        $portfolio = Portfolio::where('id', $validated['portfolio_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Verify uniqueness (one post per portfolio)
        if ($portfolio->communityPost()->exists()) {
            return back()->withErrors(['portfolio_id' => 'Este portafolio ya ha sido publicado.']);
        }

        CommunityPost::create([
            'user_id' => Auth::id(),
            'portfolio_id' => $portfolio->id,
            'title' => $portfolio->title,
            'content' => $validated['content'],
            'views_count' => 0,
        ]);

        return back()->with('success', '¡Publicación creada exitosamente!');
    }

    /**
     * Show a specific post and its threaded comments
     */
    public function show($id)
    {
        $post = CommunityPost::with(['user', 'portfolio'])
            ->withCount(['comments', 'ratings'])
            ->withAvg('ratings', 'rating')
            ->findOrFail($id);
            
        // Increment views
        $post->increment('views_count');

        $userRating = $post->ratings()
            ->where('user_id', Auth::id())
            ->value('rating');
        $hasRated = $userRating !== null;

        // Fetch comments with nesting
        // For simplicity in MVP, we might fetch flat and rebuild tree in JS, 
        // or fetch top-level and eager load children. 
        // Let's fetch top level with recursive children for 2-3 levels or fetch all and key by parent.
        // Easier: Fetch all comments for this post and let Frontend organize tree, OR fetch nested.
        
        $comments = collect();
        if ($hasRated) {
            $userWithRating = function ($query) use ($post) {
                $query->select('id', 'first_name', 'last_name', 'profile_photo_path')
                      ->with(['communityPostRatings' => function ($q) use ($post) {
                          $q->where('community_post_id', $post->id)->select('user_id', 'rating');
                      }]);
            };

            $comments = $post->comments()
                ->with(['user' => $userWithRating])
                ->whereNull('parent_id') // Top level
                ->with(['replies.user' => $userWithRating]) // Second level
                ->with(['replies.replies.user' => $userWithRating]) // Third level
                ->latest()
                ->get();
        }

        return Inertia::render('Dashboard/Community/Show', [
            'post' => $post,
            'comments' => $comments,
            'hasRated' => $hasRated,
            'userRating' => $userRating,
            'averageRating' => $post->ratings_avg_rating ? round($post->ratings_avg_rating, 2) : 0,
        ]);
    }

    /**
     * Eliminar publicación (solo autor)
     */
    public function destroy($id)
    {
        $post = CommunityPost::findOrFail($id);

        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $post->delete();

        return redirect()->route('dashboard.community')->with('success', 'Publicación eliminada correctamente.');
    }

    /**
     * Calificar publicación (1-5)
     */
    public function rate(Request $request, $id)
    {
        $post = CommunityPost::findOrFail($id);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        CommunityPostRating::updateOrCreate(
            [
                'community_post_id' => $post->id,
                'user_id' => Auth::id(),
            ],
            [
                'rating' => $validated['rating'],
            ]
        );

        return back();
    }
}
