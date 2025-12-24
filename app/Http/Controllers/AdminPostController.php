<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class AdminPostController extends Controller
{
    /**
     * List all posts with pending/rejected at the top.
     */
    public function index()
    {
        $posts = Post::with('user')
            ->orderByRaw("FIELD(status, 'pending', 'rejected', 'published')")
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show a single post (full details).
     */
    public function show(Post $post)
    {
        $post->load('user');
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Update status via AJAX.
     */
    public function updateStatus(Request $request, Post $post)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,published,rejected',
        ]);

        $post->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'status' => $post->status,
            'message' => 'Status updated successfully.',
        ]);
    }
}