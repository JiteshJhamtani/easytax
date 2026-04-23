<?php $__env->startSection('title', $page->title); ?>

<?php $__env->startSection('content'); ?>
    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="font-weight-bold mb-4 text-dark"><?php echo e($page->title); ?></h1>
                        <hr class="mb-5">
                        <div class="page-content text-secondary" style="line-height: 1.8; font-size: 1.05rem;">
                            
                            <?php echo $page->content; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .page-content h1, .page-content h2, .page-content h3, .page-content h4, .page-content h5, .page-content h6 {
        color: #1a202c;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    .page-content a {
        color: #0044b2;
        text-decoration: underline;
    }
    .page-content a:hover {
        color: #00368c;
    }
    .page-content ul, .page-content ol {
        margin-bottom: 1.5rem;
        padding-left: 1.5rem;
    }
    .page-content p {
        margin-bottom: 1.5rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.front.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/front/pages/show.blade.php ENDPATH**/ ?>