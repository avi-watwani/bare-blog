<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Posts - {{ config('app.name', 'Laravel') }}</title>
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
</head>
<body class="bg-light">
    <header class="border-bottom bg-white shadow-sm">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-3">
                <a href="{{ route('posts.index') }}" class="text-decoration-none">
                    <h5 class="mb-0 fw-bold text-dark">Laravel Blog</h5>
                </a>
                <nav class="d-flex gap-3 align-items-center">
                    <a href="{{ route('posts.index') }}" class="text-decoration-none text-secondary">All Posts</a>
                    <a href="{{ route('posts.create') }}" class="text-decoration-none text-secondary">Create Post</a>
                    <a href="{{ url('/dashboard') }}" class="text-decoration-none text-secondary">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link text-secondary p-0 border-0 text-decoration-none">Logout</button>
                    </form>
                </nav>
            </div>
        </div>
    </header>

    <main class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold">My Posts</h1>
            <a href="{{ route('posts.create') }}" class="btn btn-primary">
                Create New Post
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            @forelse($posts as $post)
                <div class="col-12">
                    <div class="card shadow-sm">
                        @if($post->image)
                            <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}" class="card-img-top" style="height: 300px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h2 class="card-title mb-0">{{ $post->title }}</h2>
                                <span class="badge 
                                    @if($post->status === 'published') bg-success
                                    @elseif($post->status === 'pending') bg-warning
                                    @else bg-danger
                                    @endif">
                                    {{ ucfirst($post->status) }}
                                </span>
                            </div>
                            <p class="text-muted small mb-3">
                                <time datetime="{{ $post->created_at->toIso8601String() }}">
                                    {{ $post->created_at->format('M d, Y') }}
                                </time>
                                @if($post->updated_at != $post->created_at)
                                    • Updated {{ $post->updated_at->format('M d, Y') }}
                                @endif
                            </p>
                            <p class="card-text">{{ Str::limit($post->content, 200) }}{{ strlen($post->content) > 200 ? '...' : '' }}</p>
                            <div class="d-flex gap-2 mt-3 pt-3 border-top">
                                @if($post->status !== 'published')
                                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-primary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('posts.destroy', $post) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                @else
                                    <span class="text-muted small">Edit/Delete available only for pending or rejected posts.</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card text-center py-5">
                        <div class="card-body">
                            <p class="text-muted mb-3">
                                You haven't created any posts yet.
                            </p>
                            <a href="{{ route('posts.create') }}" class="btn btn-primary">
                                Create Your First Post
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        @if($posts->hasPages())
            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        @endif
    </main>

    @if (!file_exists(public_path('build/manifest.json')) && !file_exists(public_path('hot')))
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @endif
</body>
</html>
