<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
        <i class="far fa-bell"></i>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($unreadCount) && $unreadCount > 0): ?>
            <span class="badge badge-warning navbar-badge"><?php echo e($unreadCount); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
        <span class="dropdown-item dropdown-header"><?php echo e($unreadCount ?? 0); ?> Unread Notifications</span>
        <div class="dropdown-divider"></div>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($unreadNotifications) && $unreadNotifications->count() > 0): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $unreadNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <a href="<?php echo e($notification->data['url'] ?? '#'); ?>" class="dropdown-item" onclick="event.preventDefault(); markNotificationAsRead('<?php echo e($notification->id); ?>', '<?php echo e($notification->data['url'] ?? '#'); ?>')">
                    <i class="fas fa-envelope mr-2"></i> <?php echo e(Str::limit($notification->data['message'] ?? 'New Notification', 30)); ?>

                    <span class="float-right text-muted text-sm"><?php echo e($notification->created_at->diffForHumans()); ?></span>
                </a>
                <div class="dropdown-divider"></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <?php else: ?>
            <a href="#" class="dropdown-item text-center text-muted">
                No new notifications
            </a>
            <div class="dropdown-divider"></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <a href="<?php echo e(route('admin.notifications.index')); ?>" class="dropdown-item dropdown-footer">See All Notifications</a>
    </div>
</li>

<script>
function markNotificationAsRead(id, redirectUrl) {
    fetch(`/admin/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
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
<?php /**PATH /var/www/uat.easytax.live/resources/views/vendor/adminlte/partials/navbar/menu-item/notifications.blade.php ENDPATH**/ ?>