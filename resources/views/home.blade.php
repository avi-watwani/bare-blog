<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Blog Posts</title>
    
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
                    @auth
                        <a href="{{ route('posts.index') }}" class="text-decoration-none text-secondary">Home</a>
                        <a href="{{ route('users.posts.index', auth()->user()) }}" class="text-decoration-none text-secondary">My Posts</a>
                        <a href="{{ url('/dashboard') }}" class="text-decoration-none text-secondary">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link text-secondary p-0 border-0 text-decoration-none">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-decoration-none text-secondary">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-decoration-none text-secondary">Register</a>
                        @endif
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <main class="container my-5">
        @auth
            <div class="d-flex justify-content-end mb-4">
                <a href="{{ route('posts.create') }}" class="btn btn-primary">
                    Create Post
                </a>
            </div>
        @endauth

        <div class="row g-4">
            @forelse($posts as $post)
                <div class="col-12">
                    <article class="card shadow-sm">
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
                                By {{ $post->user->name }} • {{ $post->created_at->format('M d, Y') }}
                            </p>
                            <p class="card-text">{{ $post->content }}</p>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="card text-center py-5">
                        <div class="card-body">
                            <p class="text-muted mb-3">
                                No posts yet. 
                                @auth
                                    <a href="{{ route('posts.create') }}" class="text-decoration-none">Create the first post!</a>
                                @else
                                    <a href="{{ route('login') }}" class="text-decoration-none">Log in to create a post.</a>
                                @endauth
                            </p>
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
