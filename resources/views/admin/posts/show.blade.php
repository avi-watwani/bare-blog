<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Post Details - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 fw-bold">{{ $post->title }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
            <div class="btn-group">
                <button class="btn btn-sm btn-success js-status" data-status="published" data-post-id="{{ $post->id }}">Publish</button>
                <button class="btn btn-sm btn-warning js-status" data-status="pending" data-post-id="{{ $post->id }}">Pending</button>
                <button class="btn btn-sm btn-danger js-status" data-status="rejected" data-post-id="{{ $post->id }}">Reject</button>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <p class="text-muted mb-1"><strong>Author:</strong> {{ $post->user->name }}</p>
            <p class="text-muted mb-2"><strong>Submitted:</strong> {{ $post->created_at->format('M d, Y H:i') }}</p>
            <p class="mb-0">
                <strong>Status:</strong> 
                <span class="badge
                    @if($post->status === 'published') bg-success
                    @elseif($post->status === 'pending') bg-warning
                    @else bg-danger
                    @endif" id="status-badge">
                    {{ ucfirst($post->status) }}
                </span>
            </p>
        </div>
    </div>

    @if($post->image)
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="card-title">Image</h5>
                <img src="{{ Storage::url($post->image) }}" class="img-fluid rounded" alt="{{ $post->title }}" style="max-height: 500px;">
            </div>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Content</h5>
            <div class="card-text" style="white-space: pre-wrap;">{{ $post->content }}</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.js-status').forEach(button => {
        button.addEventListener('click', async function() {
            const postId = this.dataset.postId;
            const status = this.dataset.status;
            const statusBadge = document.getElementById('status-badge');

            try {
                const response = await fetch(`/admin/posts/${postId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status })
                });

                const data = await response.json();

                if (data.success) {
                    // Update badge
                    statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                    statusBadge.className = 'badge';
                    
                    if (data.status === 'published') {
                        statusBadge.classList.add('bg-success');
                    } else if (data.status === 'pending') {
                        statusBadge.classList.add('bg-warning');
                    } else {
                        statusBadge.classList.add('bg-danger');
                    }

                    // Show success message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                    alert.style.zIndex = '9999';
                    alert.innerHTML = `
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(alert);

                    setTimeout(() => {
                        alert.remove();
                    }, 3000);
                } else {
                    alert('Failed to update status');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while updating the status');
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
