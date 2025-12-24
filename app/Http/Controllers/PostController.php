<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')
            ->where('status', 'published')
            ->latest()
            ->paginate(10);

        return view('home', compact('posts'));
    }

    public function userPosts(User $user)
    {
        if (auth()->id() !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $posts = Post::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('posts.user-posts', compact('posts', 'user'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        $validated['status'] = 'pending';

        $request->user()->posts()->create($validated);

        return redirect()->route('users.posts.index', auth()->user())
            ->with('success', 'Post submitted and pending approval.');
    }

    public function edit(Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        if ($post->status === 'published') {
            abort(403, 'Only pending or rejected posts can be edited.');
        }

        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        if ($post->status === 'published') {
            abort(403, 'Only pending or rejected posts can be updated.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
        ]);

        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($validated);

        return redirect()->route('users.posts.index', auth()->user())->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        if ($post->status === 'published') {
            abort(403, 'Only pending or rejected posts can be deleted.');
        }

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()->route('users.posts.index', auth()->user())->with('success', 'Post deleted successfully!');
    }
}
