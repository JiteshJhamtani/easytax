
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'value', 'icon']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['title', 'value', 'icon']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="col-lg-4 col-md-6 col-sm-12">
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon-green-soft">
            <?php echo $__env->make($icon, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div class="kpi-info">
            <div class="kpi-label"><?php echo e($title); ?></div>
            <div class="kpi-value text-dark"><?php echo e($value); ?></div>
        </div>
    </div>
</div><?php /**PATH /var/www/uat.easytax.live/resources/views/components/agent/kpi-card.blade.php ENDPATH**/ ?>