<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['service', 'commission', 'toPay']));

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

foreach (array_filter((['service', 'commission', 'toPay']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<div id="pmc-backdrop" class="hidden fixed inset-0 w-screen h-screen bg-slate-900/70 backdrop-blur-sm z-[9999] flex items-center justify-center p-4 transition-opacity" style="margin: 0; padding: 1rem; top: 0; left: 0;">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 w-full max-w-md relative shadow-2xl">
        
        
        <button id="pmc-x" type="button" aria-label="Close" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-2xl leading-none transition-colors">
            &times;
        </button>

        <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider mb-2">Confirm Payment</p>
        <p class="text-xl font-extrabold text-gray-900 dark:text-white mb-6 leading-tight"><?php echo e($service->name); ?></p>

        <div class="bg-gray-50 dark:bg-slate-900/50 rounded-xl p-5 mb-6 border border-gray-100 dark:border-slate-700">
            <div class="flex justify-between mb-3">
                <span class="text-sm text-gray-500 dark:text-gray-400 font-semibold">Service fee</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white"><?php echo e(money($service->price)); ?></span>
            </div>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($commission > 0): ?>
                <div class="flex justify-between mb-4">
                    <span class="text-sm text-gray-500 dark:text-gray-400 font-semibold">Your commission</span>
                    <span class="text-sm font-extrabold text-[#1E9C5D]">&#8722; <?php echo e(money($commission)); ?></span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            
            <div class="border-t border-gray-200 dark:border-slate-700 pt-4 flex justify-between items-center">
                <span class="text-base font-extrabold text-gray-900 dark:text-white">You pay</span>
                <span class="text-3xl font-extrabold text-gray-900 dark:text-white leading-none"><?php echo e(money($toPay)); ?></span>
            </div>
        </div>

        <p class="text-sm text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($commission > 0): ?>
                Your commission of <strong class="text-gray-900 dark:text-white"><?php echo e(money($commission)); ?></strong> is deducted instantly. 
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            You will proceed to Razorpay to complete the payment of <strong class="text-gray-900 dark:text-white"><?php echo e(money($toPay)); ?></strong>.
        </p>

        <div class="flex gap-3">
            <button id="pmc-cancel" type="button" class="flex-1 py-3 text-sm font-bold rounded-lg bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Cancel
            </button>
            <button id="pmc-confirm" type="button" class="flex-[2] py-3 text-sm font-bold rounded-lg bg-[#1E9C5D] text-white hover:bg-[#157a48] hover:-translate-y-px transition-all shadow-lg shadow-green-500/30">
                Pay <?php echo e(money($toPay)); ?>

            </button>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('pmc-backdrop');
        if (modal) {
            // This rips the modal out of any restrictive parent containers
            // and places it directly on the body, fixing the CSS 'fixed' bug forever.
            document.body.appendChild(modal);
        }
    });
</script><?php /**PATH /var/www/uat.easytax.live/resources/views/components/payment-modal.blade.php ENDPATH**/ ?>