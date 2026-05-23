@extends('adminlte::page')

@section('title', 'Notifications')

@section('content_header')
    <h1 class="m-0 text-dark font-weight-bold">
        <i class="fas fa-bell text-primary mr-2"></i> Notifications
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <table class="responsive-card-table table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Message</th>
                                <th>Time</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notifications as $notification)
                                <tr class="{{ empty($notification->read_at) ? 'font-weight-bold bg-light' : '' }}">
                                    <td>
                                        <a href="{{ $notification->data['url'] ?? '#' }}" class="text-dark">
                                            {{ $notification->data['message'] ?? 'Notification' }}
                                        </a>
                                    </td>
                                    <td>{{ $notification->created_at->diffForHumans() }}</td>
                                    <td>
                                        @empty($notification->read_at)
                                            <button onclick="markNotificationAsRead('{{ $notification->id }}', false)" class="btn btn-sm btn-outline-primary">Mark Read</button>
                                        @endempty
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No notifications found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
                @if($notifications->hasPages())
                    <div class="card-footer clearfix">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

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
        if(redirectUrl) {
            window.location.href = redirectUrl;
        } else {
            window.location.reload();
        }
    });
}
</script>
@stop
