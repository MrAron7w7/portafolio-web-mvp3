<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommunityPost;
use App\Models\CommunityComment;
use Illuminate\Support\Facades\Auth;

class CommunityCommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $post = CommunityPost::findOrFail($postId);

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:community_comments,id'
        ]);

        // Validate that parent actually belongs to this post if present
        if ($request->parent_id) {
            $parent = CommunityComment::find($request->parent_id);
            if ($parent->community_post_id !== $post->id) {
                return back()->withErrors(['parent_id' => 'Invalid parent comment.']);
            }
        }

        CommunityComment::create([
            'user_id' => Auth::id(),
            'community_post_id' => $post->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content'],
        ]);

        return back(); // Inertia will handle the partial reload
    }

    public function update(Request $request, CommunityComment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update([
            'content' => $validated['content'],
        ]);

        return back();
    }

    public function destroy(CommunityComment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $comment->delete();

        return back();
    }
}
