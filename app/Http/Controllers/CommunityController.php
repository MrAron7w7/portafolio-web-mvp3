<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Inertia\Inertia;
use App\Models\CommunityComment;
use Illuminate\Support\Facades\Auth;

class CommunityController extends Controller
{
    /**
     * Public Community Page
     * Shows mostly positive/featured comments
     */
    public function index()
    {
        // Obtener todos los comentarios recientes para el público
        $comments = CommunityComment::with('user:id,first_name,last_name,profile_photo_path')
            ->latest()
            ->take(50)
            ->get();

        return Inertia::render('Community/PublicIndex', [
            'comments' => $comments
        ]);
    }

    /**
     * Dashboard Community Section (Mini-Forum)
     * Shows all comments in a timeline
     */
    public function dashboardIndex()
    {
        $comments = CommunityComment::with('user:id,first_name,last_name,profile_photo_path')
            ->latest()
            ->paginate(10);

        return Inertia::render('Dashboard/Community/Index', [
            'comments' => $comments
        ]);
    }

    /**
     * Store a new comment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        CommunityComment::create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'rating' => $validated['rating'],
            'is_featured' => false,
            'type' => 'detailed'
        ]);

        return back()->with('success', '¡Gracias por tu comentario!');
    }
}
