@props(['service', 'commission', 'toPay'])

{{-- NATIVE TAILWIND PAYMENT CONFIRMATION MODAL --}}
<div id="pmc-backdrop" class="hidden fixed inset-0 w-screen h-screen bg-slate-900/70 backdrop-blur-sm z-[9999] flex items-center justify-center p-4 transition-opacity" style="margin: 0; padding: 1rem; top: 0; left: 0;">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 w-full max-w-md relative shadow-2xl">
        
        {{-- Close Button --}}
        <button id="pmc-x" type="button" aria-label="Close" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-2xl leading-none transition-colors">
            &times;
        </button>

        <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider mb-2">Confirm Payment</p>
        <p class="text-xl font-extrabold text-gray-900 dark:text-white mb-6 leading-tight">{{ $service->name }}</p>

        <div class="bg-gray-50 dark:bg-slate-900/50 rounded-xl p-5 mb-6 border border-gray-100 dark:border-slate-700">
            <div class="flex justify-between mb-3">
                <span class="text-sm text-gray-500 dark:text-gray-400 font-semibold">Service fee</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ money($service->price) }}</span>
            </div>
            
            @if ($commission > 0)
                <div class="flex justify-between mb-4">
                    <span class="text-sm text-gray-500 dark:text-gray-400 font-semibold">Your commission</span>
                    <span class="text-sm font-extrabold text-[#1E9C5D]">&#8722; {{ money($commission) }}</span>
                </div>
            @endif
            
            <div class="border-t border-gray-200 dark:border-slate-700 pt-4 flex justify-between items-center">
                <span class="text-base font-extrabold text-gray-900 dark:text-white">You pay</span>
                <span class="text-3xl font-extrabold text-gray-900 dark:text-white leading-none">{{ money($toPay) }}</span>
            </div>
        </div>

        <p class="text-sm text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
            @if ($commission > 0)
                Your commission of <strong class="text-gray-900 dark:text-white">{{ money($commission) }}</strong> is deducted instantly. 
            @endif
            You will proceed to Razorpay to complete the payment of <strong class="text-gray-900 dark:text-white">{{ money($toPay) }}</strong>.
        </p>

        <div class="flex gap-3">
            <button id="pmc-cancel" type="button" class="flex-1 py-3 text-sm font-bold rounded-lg bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Cancel
            </button>
            <button id="pmc-confirm" type="button" class="flex-[2] py-3 text-sm font-bold rounded-lg bg-[#1E9C5D] text-white hover:bg-[#157a48] hover:-translate-y-px transition-all shadow-lg shadow-green-500/30">
                Pay {{ money($toPay) }}
            </button>
        </div>
    </div>
</div>

{{-- MODAL TELEPORT FIX --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('pmc-backdrop');
        if (modal) {
            // This rips the modal out of any restrictive parent containers
            // and places it directly on the body, fixing the CSS 'fixed' bug forever.
            document.body.appendChild(modal);
        }
    });
</script>