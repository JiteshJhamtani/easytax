<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
        <i class="far fa-bell"></i>
        @if(isset($unreadCount) && $unreadCount > 0)
            <span class="badge badge-warning navbar-badge">{{ $unreadCount }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
        <span class="dropdown-item dropdown-header">{{ $unreadCount ?? 0 }} Unread Notifications</span>
        <div class="dropdown-divider"></div>
        
        @if(isset($unreadNotifications) && $unreadNotifications->count() > 0)
            @foreach($unreadNotifications as $notification)
                <a href="{{ $notification->data['url'] ?? '#' }}" class="dropdown-item" onclick="event.preventDefault(); markNotificationAsRead('{{ $notification->id }}', '{{ $notification->data['url'] ?? '#' }}')">
                    <i class="fas fa-envelope mr-2"></i> {{ Str::limit($notification->data['message'] ?? 'New Notification', 30) }}
                    <span class="float-right text-muted text-sm">{{ $notification->created_at->diffForHumans() }}</span>
                </a>
                <div class="dropdown-divider"></div>
            @endforeach
        @else
            <a href="#" class="dropdown-item text-center text-muted">
                No new notifications
            </a>
            <div class="dropdown-divider"></div>
        @endif
        
        <a href="{{ route('admin.notifications.index') }}" class="dropdown-item dropdown-footer">See All Notifications</a>
    </div>
</li>

<script>
function markNotificationAsRead(id, redirectUrl) {
    fetch(`/admin/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(() => {
        if(redirectUrl && redirectUrl !== '#') {
            window.location.href = redirectUrl;
        } else {
            window.location.reload();
        }
    });
}
</script>
