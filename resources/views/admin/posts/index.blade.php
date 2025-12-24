<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All Posts - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 fw-bold">All Posts</h1>
        <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary btn-sm">Dashboard</a>
    </div>

    <div id="alert-container"></div>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($posts as $post)
                <tr data-post-id="{{ $post->id }}">
                    <td><strong>{{ $post->title }}</strong></td>
                    <td>{{ $post->user->name }}</td>
                    <td>
                        <span class="badge status-badge
                            @if($post->status === 'published') bg-success
                            @elseif($post->status === 'pending') bg-warning
                            @else bg-danger
                            @endif">
                            {{ ucfirst($post->status) }}
                        </span>
                    </td>
                    <td>{{ $post->created_at->format('M d, Y') }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.posts.show', $post) }}" class="btn btn-sm btn-outline-primary">View</a>
                        <div class="btn-group ms-2">
                            <button class="btn btn-sm btn-success js-status" data-status="published" data-post-id="{{ $post->id }}">Publish</button>
                            <button class="btn btn-sm btn-warning js-status" data-status="pending" data-post-id="{{ $post->id }}">Pending</button>
                            <button class="btn btn-sm btn-danger js-status" data-status="rejected" data-post-id="{{ $post->id }}">Reject</button>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $posts->links() }}
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const alertContainer = document.getElementById('alert-container');

    function showAlert(message, type = 'success') {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.appendChild(alert);

        setTimeout(() => {
            alert.remove();
        }, 3000);
    }

    document.querySelectorAll('.js-status').forEach(button => {
        button.addEventListener('click', async function() {
            const postId = this.dataset.postId;
            const status = this.dataset.status;
            const row = this.closest('tr');
            const badge = row.querySelector('.status-badge');

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
                    badge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                    badge.className = 'badge status-badge';
                    
                    if (data.status === 'published') {
                        badge.classList.add('bg-success');
                    } else if (data.status === 'pending') {
                        badge.classList.add('bg-warning');
                    } else {
                        badge.classList.add('bg-danger');
                    }

                    showAlert(data.message);

                    // Move row to top if status changed to pending/rejected
                    if (data.status === 'pending' || data.status === 'rejected') {
                        const tbody = row.parentElement;
                        tbody.insertBefore(row, tbody.firstChild);
                    }
                } else {
                    showAlert('Failed to update status', 'danger');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('An error occurred while updating the status', 'danger');
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
