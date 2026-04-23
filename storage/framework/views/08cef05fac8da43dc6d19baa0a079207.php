<header class="header" id="mainHeader">
    <div class="container header__container">
        <a href="<?php echo e(url('/')); ?>" class="header__logo">
            EasyTax<span class="logo-dot">.</span>
        </a>

        <nav class="header__nav" aria-label="Main Navigation">
            <ul class="header__nav-list">
                <li><a href="<?php echo e(url('/')); ?>"
                        class="header__nav-link <?php echo e(request()->is('/') ? 'active' : ''); ?>">Home</a></li>
                <li><a href="<?php echo e(route('services.index')); ?>"
                        class="header__nav-link <?php echo e(request()->routeIs('services.*') ? 'active' : ''); ?>">Services</a>
                </li>
                <li><a href="#" class="header__nav-link">Resources</a></li>
                <li><a href="#" class="header__nav-link">Support</a></li>
            </ul>
        </nav>

        <div class="header__actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
                <a href="<?php echo e(route('login')); ?>" class="btn btn-primary btn-sm btn-login">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    Agent Login
                </a>
            <?php else: ?>
                <a href="<?php echo e(auth()->user()->role === 'ADMIN' ? route('admin.dashboard') : route('agent.dashboard')); ?>" class="btn btn-primary btn-sm btn-login">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    Dashboard
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <button class="header__toggle" id="mobileNavToggle" aria-expanded="false" aria-label="Toggle navigation">
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
        </button>
    </div>

    <nav class="mobile-nav" id="mobileNav" aria-hidden="true">
        <div class="container">
            <ul class="mobile-nav__list">
                <li><a href="<?php echo e(url('/')); ?>" class="mobile__nav-link">Home</a></li>
                <li><a href="<?php echo e(route('services.index')); ?>" class="mobile__nav-link">Services</a></li>
                <li><a href="#" class="mobile__nav-link">Resources</a></li>
                <li><a href="#" class="mobile__nav-link">Support</a></li>
            </ul>
            <div class="mobile-nav__footer">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-primary w-100 btn-login">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                        Agent Login
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(auth()->user()->role === 'ADMIN' ? route('admin.dashboard') : route('agent.dashboard')); ?>" class="btn btn-primary w-100 btn-login">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        Dashboard
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<?php /**PATH /var/www/uat.easytax.live/resources/views/components/front/header.blade.php ENDPATH**/ ?>