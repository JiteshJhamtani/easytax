<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'EasyTax')); ?></title>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    

    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>

    <style>
        .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        html.transitioning, html.transitioning *, html.transitioning *:before, html.transitioning *:after {
            transition: all 300ms !important; transition-delay: 0 !important;
        }
    </style>
</head>
<body class="antialiased min-h-screen w-full transition-colors duration-300 bg-[#f4f7f5] dark:bg-[#0f172a]">

    <div class="flex min-h-screen items-center justify-center p-4 md:p-8">
        <div id="auth-container" class="w-full max-w-6xl overflow-hidden rounded-2xl relative opacity-0 scale-95 transition-all duration-500 ease-out bg-white shadow-2xl shadow-gray-200 dark:bg-slate-800 dark:shadow-xl dark:shadow-slate-900/50">
            
            
            <button id="theme-toggle" class="absolute right-4 top-4 rounded-full p-2 transition-colors z-10 bg-gray-100 text-gray-400 hover:bg-gray-200 dark:bg-slate-700 dark:text-yellow-400 dark:hover:bg-slate-600">
                <svg id="theme-toggle-light-icon" class="hidden w-[18px] h-[18px] animate-pulse" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="4"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path>
                </svg>
                <svg id="theme-toggle-dark-icon" class="w-[18px] h-[18px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                </svg>
            </button>

            <div class="flex flex-col md:flex-row min-h-[600px]">
                
                <div class="hidden md:block w-full md:w-3/5 p-6 animate-fade-in bg-[#f8faf9] dark:bg-slate-900/50">
                    <div class="grid grid-cols-2 grid-rows-3 gap-4 h-full overflow-hidden">
                        
                        <div class="overflow-hidden rounded-xl shadow-sm">
                        <img src="<?php echo e(asset('assets/images/tax-pic-1.jpeg
                ')); ?>" alt="Accounting paperwork" class="w-full h-full object-cover transition-transform duration-700 hover:scale-105">
                        </div>
                        <div class="stagger-anim opacity-0 translate-y-5 rounded-xl flex flex-col justify-center items-center p-6 text-white shadow-sm transition-all duration-700 ease-out bg-[#182b49] dark:bg-[#12223a]" style="transition-delay: 0.2s;">
                            <h2 class="text-5xl font-bold mb-2 tracking-tight">15k+</h2>
                            <p class="text-center text-sm font-medium px-2 text-gray-300">Trusted users securely processing and filing returns this year.</p>
                        </div>
                        
<div class="overflow-hidden rounded-xl shadow-sm">
    <img src="<?php echo e(asset('assets/images/tax-pic-2.jpeg')); ?>" alt="Signing financial documents" class="w-full h-full object-cover transition-transform duration-700 hover:scale-105">
</div>
                        
<div class="overflow-hidden rounded-xl shadow-sm">
    <img src="<?php echo e(asset('assets/images/tax-pic-3.jpeg')); ?>" alt="Financial charts on screen" class="w-full h-full object-cover transition-transform duration-700 hover:scale-105">
</div>
                        <div class="stagger-anim opacity-0 translate-y-5 rounded-xl flex flex-col justify-center items-center p-6 text-white shadow-sm transition-all duration-700 ease-out bg-[#5da565] dark:bg-[#4d8c52]" style="transition-delay: 0.4s;">
                            
                            <h2 class="text-5xl font-bold mb-2 tracking-tight">4.9</h2>
                            <div class="flex gap-1 mb-2 text-yellow-300">
                             <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                            </div>
                            <p class="text-center text-sm font-medium px-2 text-green-50">Google Rating from our satisfied tax professionals and agents.</p>
                        </div>
                        
<div class="overflow-hidden rounded-xl shadow-sm">
    <img src="<?php echo e(asset('assets/images/tax-pic-4.jpeg')); ?>" alt="Professional consulting" class="w-full h-full object-cover transition-transform duration-700 hover:scale-105">
</div>
                    </div>
                </div>
                
                
                <div class="stagger-anim opacity-0 translate-x-5 w-full md:w-2/5 p-8 md:p-12 flex flex-col justify-center relative transition-all duration-700 ease-out bg-white text-gray-900 dark:bg-slate-800 dark:text-white">
                    <div class="max-w-sm w-full mx-auto">
                        
                        
                        <div class="flex flex-col items-center mb-8">
                            <img src="<?php echo e(asset('assets/images/logo11.png')); ?>" alt="Easy Tax Logo" class="h-[6.5rem] w-auto object-contain" onerror="this.style.display='none'; document.getElementById('fallback-logo').classList.remove('hidden'); document.getElementById('fallback-logo').classList.add('flex');" />
                            <div id="fallback-logo" class="hidden flex-col items-center">
                                <svg viewBox="0 0 120 100" class="w-[5.5rem] h-auto mx-auto mb-1">
                                    <defs><clipPath id="circle-clip-new"><circle cx="60" cy="45" r="38" /></clipPath></defs>
                                    <circle cx="60" cy="45" r="38" class="fill-[#14314c] dark:fill-[#1e3458]" />
                                    <g clip-path="url(#circle-clip-new)">
                                        <polygon points="30,70 30,34 40,30 40,80" fill="white" />
                                        <polygon points="44,70 44,22 54,18 54,80" fill="white" />
                                        <polygon points="58,60 58,10 68,6 68,80" fill="white" />
                                    </g>
                                    <path d="M 22 66 L 46 86 L 96 32 C 86 64 68 88 52 94 C 38 98 28 85 22 66 Z" fill="#18a149" />
                                </svg>
                                <div class="text-center">
                                    <h2 class="text-[2.1rem] font-black tracking-tight flex items-center justify-center leading-none mb-1">
                                        <span class="text-[#14314c] dark:text-white">EASY</span>
                                        <span class="text-[#18a149] ml-2">TAX</span>
                                    </h2>
                                    <p class="text-[9.5px] font-bold tracking-[0.16em] uppercase text-[#333333] dark:text-gray-400">Tax Return Business</p>
                                </div>
                            </div>
                        </div>

                        
                        <?php echo e($slot); ?>


                        <div class="mt-8 text-center">
                            <p class="text-[13px] text-[#8ba3b5] dark:text-gray-500">
                                © <?php echo e(date('Y')); ?> EasyTax. All rights reserved.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Entrance animations
            setTimeout(() => {
                const container = document.getElementById('auth-container');
                container.classList.remove('opacity-0', 'scale-95');
                container.classList.add('opacity-100', 'scale-100');
                document.querySelectorAll('.stagger-anim').forEach(el => {
                    el.classList.remove('opacity-0', 'translate-y-5', 'translate-x-5');
                });
            }, 100);

            // Dynamic Show/Hide Password functionality (Works for both Login and Register)
            document.querySelectorAll('.toggle-password-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    this.textContent = type === 'password' ? 'Show' : 'Hide';
                });
            });

            // Dark Mode Toggle
            const themeToggleBtn = document.getElementById('theme-toggle');
            const darkIcon = document.getElementById('theme-toggle-light-icon');
            const lightIcon = document.getElementById('theme-toggle-dark-icon');
            
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                lightIcon.classList.add('hidden'); darkIcon.classList.remove('hidden');
            } else {
                document.documentElement.classList.remove('dark');
            }

            themeToggleBtn.addEventListener('click', function() {
                document.documentElement.classList.add('transitioning');
                darkIcon.classList.toggle('hidden'); lightIcon.classList.toggle('hidden');
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark'); localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark'); localStorage.setItem('color-theme', 'dark');
                }
                setTimeout(() => document.documentElement.classList.remove('transitioning'), 300);
            });

            // Loading Spinner
            const authForm = document.getElementById('authForm');
            if(authForm) {
                authForm.addEventListener('submit', () => {
                    const btn = document.getElementById('submitBtn');
                    btn.disabled = true; btn.classList.add('cursor-not-allowed', 'opacity-80');
                    document.getElementById('btnText').textContent = 'Processing...';
                    document.getElementById('btnSpinner').classList.remove('hidden');
                });
            }
        });
    </script>
</body>
</html><?php /**PATH /var/www/uat.easytax.live/resources/views/layouts/guest.blade.php ENDPATH**/ ?>