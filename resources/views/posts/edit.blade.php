<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Post - {{ config('app.name', 'Laravel') }}</title>
    
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
                    <a href="{{ route('users.posts.index', auth()->user()) }}" class="text-decoration-none text-secondary">My Posts</a>
                    <a href="{{ route('posts.index') }}" class="text-decoration-none text-secondary">All Posts</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="container my-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="card-title mb-4">Edit Post</h1>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('posts.update', $post) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="title" class="form-label">
                            Title <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            class="form-control @error('title') is-invalid @enderror" 
                            id="title" 
                            name="title" 
                            value="{{ old('title', $post->title) }}"
                            required
                        >
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">
                            Content <span class="text-danger">*</span>
                        </label>
                        <textarea 
                            class="form-control @error('content') is-invalid @enderror" 
                            id="content" 
                            name="content" 
                            rows="10"
                            required
                        >{{ old('content', $post->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($post->image)
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div>
                                <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}" class="img-fluid rounded" style="max-height: 300px;">
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="image" class="form-label">
                            @if($post->image)
                                Replace Image
                            @else
                                Image
                            @endif
                        </label>
                        <input 
                            type="file" 
                            class="form-control @error('image') is-invalid @enderror" 
                            id="image" 
                            name="image" 
                            accept="image/jpeg,image/png,image/jpg,image/gif"
                        >
                        <div class="form-text">Accepted formats: JPEG, PNG, JPG, GIF. Max size: 1MB</div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update Post</button>
                        <a href="{{ route('users.posts.index', auth()->user()) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    @if (!file_exists(public_path('build/manifest.json')) && !file_exists(public_path('hot')))
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @endif
</body>
</html>
