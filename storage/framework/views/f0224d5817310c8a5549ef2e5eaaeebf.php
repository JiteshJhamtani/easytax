<?php $__env->startSection('title', 'EasyTax | Your Tax Partner for Personalized Solutions'); ?>

<?php $__env->startSection('content'); ?>
    <section class="hero-section">
        <div class="hero-background-pattern"></div>
        <div class="container hero-container">
            <div class="hero-content">
                <div class="hero-badge">
                    <span class="badge-icon">★</span> 4.9 Google Rating | 10k+ Trusted Users
                </div>

                <h1 class="hero-title">
                    Expert Guidance <br>
                    <span class="text-highlight">Every Step of the Way.</span>
                </h1>

                <p class="hero-subtitle">
                    Ensuring maximum returns and total peace of mind for you and your business. Streamline your ITR filing,
                    GST registration, and tax compliance with our certified professionals.
                </p>

                <div class="hero-actions">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
                        <a href="<?php echo e(route('login')); ?>" class="btn btn-primary btn-lg shadow-primary">
                            Agent Portal Login
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                <polyline points="10 17 15 12 10 7"></polyline>
                                <line x1="15" y1="12" x2="3" y2="12"></line>
                            </svg>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(auth()->user()->role === 'ADMIN' ? route('admin.dashboard') : route('agent.dashboard')); ?>" class="btn btn-primary btn-lg shadow-primary">
                            Dashboard
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <a href="<?php echo e(route('services.index')); ?>" class="btn btn-outline-white btn-lg">
                        Explore All Services
                    </a>
                </div>

                <div class="hero-stats">
                    <div class="stat-item">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#00B259"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <span>ISO 27001 Certified</span>
                    </div>
                    <div class="stat-item">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#00B259"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span>128-bit SSL Encryption</span>
                    </div>
                </div>
            </div>

            <div class="hero-visual">
                <div class="floating-card card-1">
                    <div class="card-icon bg-orange-light"><svg width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="#FF6B00" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg></div>
                    <div class="card-text">
                        <h4>File ITR Yourself</h4>
                        <p>Starting at just ₹49</p>
                    </div>
                </div>
                <div class="floating-card card-2">
                    <div class="card-icon bg-green-light"><svg width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="#00B259" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg></div>
                    <div class="card-text">
                        <h4>GST Registration</h4>
                        <p>Done in just 7 days</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="services-section">
        <div class="container">
            <div class="section-heading-center">
                <h2 class="title">Popular Services</h2>
                <p class="subtitle">Comprehensive solutions designed to simplify your personal and corporate tax
                    obligations.</p>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($services->count()): ?>
                <div class="services-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="service-card">
                            <div class="service-icon-wrapper">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                </svg>
                            </div>
                            <h3 class="service-title"><?php echo e($service->name); ?></h3>
                            <p class="service-desc">
                                <?php echo e(\Illuminate\Support\Str::limit($service->description, 100)); ?>

                            </p>
                            <div class="service-footer">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($service->price > 0): ?>
                                    <div class="service-price">
                                        <span class="label">Starting from</span>
                                        <span class="amount"><?php echo e(money($service->price)); ?></span>
                                    </div>
                                <?php else: ?>
                                    <div class="service-price">
                                        <span class="amount">Consultation</span>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <a href="<?php echo e(route('services.show', $service->slug)); ?>" class="btn-link">
                                    Apply Now <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M5 12h14M12 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php else: ?>
                <div class="empty-services">
                    <p>Our service catalog is currently being updated. Please contact your assigned agent.</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="text-center mt-12">
                <a href="<?php echo e(route('services.index')); ?>" class="btn btn-outline-primary">View All Services</a>
            </div>
        </div>
    </section>

    <section class="process-section bg-light-blue">
        <div class="container">
            <div class="section-heading-center">
                <h2 class="title">How it works</h2>
                <p class="subtitle">5 Simple Steps to e-file your Income Tax Return with our network</p>
            </div>

            <div class="process-steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h4>Agent Login</h4>
                    <p>Access your secure portal</p>
                </div>
                <div class="step-connector"></div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h4>Quick Pick</h4>
                    <p>Select sources of income</p>
                </div>
                <div class="step-connector"></div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h4>Pre-filled Data</h4>
                    <p>Details auto-extracted</p>
                </div>
                <div class="step-connector"></div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h4>Compare Computations</h4>
                    <p>Old vs. New regimes</p>
                </div>
                <div class="step-connector"></div>
                <div class="step">
                    <div class="step-number">5</div>
                    <h4>File ITR</h4>
                    <p>Quick, easy, and secure</p>
                </div>
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="container">
            <div class="features-layout">
                <div class="features-text">
                    <h2 class="title">Why Choose EasyTax</h2>
                    <p class="subtitle">We offer a compelling combination of expertise, personalized service, and
                        convenience that sets us apart from the rest.</p>

                    <ul class="feature-list">
                        <li>
                            <div class="list-icon"><svg width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="#00B259" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg></div>
                            <div>
                                <strong>Deep Expertise</strong>
                                <p>Our team comprises seasoned tax professionals and CAs with extensive knowledge.</p>
                            </div>
                        </li>
                        <li>
                            <div class="list-icon"><svg width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="#00B259" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg></div>
                            <div>
                                <strong>Personalized Service</strong>
                                <p>We understand that every individual or business has unique tax circumstances.</p>
                            </div>
                        </li>
                        <li>
                            <div class="list-icon"><svg width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="#00B259" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg></div>
                            <div>
                                <strong>Convenience and Efficiency</strong>
                                <p>Easy-to-use platforms integrated with government portals for rapid processing.</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="features-image">
                    <div class="trust-card-large">
                        <div class="trust-card-header">
                            <span class="live-dot"></span> System Status: Operational
                        </div>
                        <h3>Ready to get started?</h3>
                        <p>Our closed-agent network ensures your data is handled strictly by vetted professionals.</p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
                            <a href="<?php echo e(route('login')); ?>" class="btn btn-primary mt-6">Login to Portal</a>
                        <?php else: ?>
                            <a href="<?php echo e(auth()->user()->role === 'ADMIN' ? route('admin.dashboard') : route('agent.dashboard')); ?>" class="btn btn-primary mt-6">Go to Dashboard</a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        :root {
            /* Vibrant Brand Colors based on high-end tax platforms */
            --color-primary: #0044B2;
            /* Trust Blue */
            --color-primary-dark: #003388;
            --color-secondary: #FF6B00;
            /* Action Orange */
            --color-secondary-hover: #E55F00;
            --color-success: #00B259;
            /* Money/Growth Green */

            --bg-main: #ffffff;
            --bg-light-blue: #F4F7FB;
            /* Very soft blue for sections */
            --bg-card: #ffffff;

            --text-dark: #1E293B;
            /* Slate 800 */
            --text-body: #475569;
            /* Slate 600 */
            --text-muted: #64748B;
            /* Slate 500 */

            --border-color: #E2E8F0;
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;

            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 10px 25px -5px rgba(0, 40, 100, 0.08);
            --shadow-primary: 0 8px 20px rgba(255, 107, 0, 0.25);

            --font-main: 'Inter', system-ui, -apple-system, sans-serif;
        }

        body {
            font-family: var(--font-main);
            color: var(--text-body);
            background-color: var(--bg-main);
            line-height: 1.6;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .mt-6 {
            margin-top: 1.5rem;
        }

        .mt-12 {
            margin-top: 3rem;
        }

        .text-center {
            text-align: center;
        }

        .bg-light-blue {
            background-color: var(--bg-light-blue);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-sm);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 2px solid transparent;
            cursor: pointer;
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.125rem;
            border-radius: var(--radius-md);
        }

        .btn-primary {
            background-color: var(--color-secondary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--color-secondary-hover);
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            border-color: var(--color-primary);
            color: var(--color-primary);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background-color: rgba(0, 68, 178, 0.05);
        }

        .btn-outline-white {
            border-color: rgba(255, 255, 255, 0.4);
            color: white;
            background: transparent;
        }

        .btn-outline-white:hover {
            background-color: white;
            color: var(--color-primary);
        }

        .shadow-primary {
            box-shadow: var(--shadow-primary);
        }

        .btn-link {
            color: var(--color-primary);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            transition: color 0.2s;
        }

        .btn-link:hover {
            color: var(--color-secondary);
        }

        /* Hero Section (Blue Background) */
        .hero-section {
            background: linear-gradient(135deg, #002B7F 0%, #0044B2 100%);
            color: white;
            padding: 6rem 0;
            position: relative;
            overflow: hidden;
        }

        .hero-background-pattern {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.5;
            z-index: 0;
        }

        .hero-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 4rem;
            position: relative;
            z-index: 1;
        }

        @media (min-width: 1024px) {
            .hero-container {
                grid-template-columns: 1.1fr 0.9fr;
                align-items: center;
            }
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 2rem;
            backdrop-filter: blur(4px);
        }

        .badge-icon {
            color: #FFD700;
            font-size: 1.1rem;
        }

        .hero-title {
            font-size: clamp(2.5rem, 4vw, 3.5rem);
            line-height: 1.15;
            font-weight: 800;
            margin-bottom: 1.5rem;
        }

        .text-highlight {
            color: #80BFFF;
        }

        /* Soft light blue contrast against dark blue */

        .hero-subtitle {
            font-size: 1.125rem;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 2.5rem;
            max-width: 540px;
            line-height: 1.7;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 3rem;
        }

        .hero-stats {
            display: flex;
            gap: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 2rem;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
        }

        /* Abstract Hero Visuals */
        .hero-visual {
            position: relative;
            height: 100%;
            min-height: 350px;
        }

        .floating-card {
            background: white;
            border-radius: var(--radius-md);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            position: absolute;
            width: 300px;
            color: var(--text-dark);
            animation: float 6s ease-in-out infinite;
        }

        .card-1 {
            top: 10%;
            right: 10%;
            animation-delay: 0s;
        }

        .card-2 {
            bottom: 20%;
            left: 0;
            animation-delay: -3s;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .bg-orange-light {
            background: rgba(255, 107, 0, 0.1);
        }

        .bg-green-light {
            background: rgba(0, 178, 89, 0.1);
        }

        .card-text h4 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .card-text p {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin: 0;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        /* Common Section Headings */
        .services-section,
        .process-section,
        .features-section {
            padding: 6rem 0;
        }

        .section-heading-center {
            text-align: center;
            max-width: 650px;
            margin: 0 auto 4rem;
        }

        .section-heading-center .title {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .section-heading-center .subtitle {
            font-size: 1.125rem;
            color: var(--text-body);
        }

        /* Services Grid */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
        }

        .service-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 2rem;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: rgba(0, 68, 178, 0.2);
        }

        .service-icon-wrapper {
            width: 56px;
            height: 56px;
            background: rgba(0, 68, 178, 0.05);
            color: var(--color-primary);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .service-card:hover .service-icon-wrapper {
            background: var(--color-primary);
            color: white;
            transition: 0.3s;
        }

        .service-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.75rem;
        }

        .service-desc {
            font-size: 0.95rem;
            color: var(--text-body);
            margin-bottom: 2rem;
            flex-grow: 1;
        }

        .service-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .service-price .label {
            display: block;
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .service-price .amount {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-dark);
        }

        /* Process Steps */
        .process-steps {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        @media (min-width: 992px) {
            .process-steps {
                flex-direction: row;
                align-items: flex-start;
                text-align: center;
            }
        }

        .step {
            flex: 1;
            position: relative;
        }

        .step-number {
            width: 48px;
            height: 48px;
            background: white;
            border: 2px solid var(--color-primary);
            color: var(--color-primary);
            font-size: 1.25rem;
            font-weight: 800;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: var(--shadow-sm);
        }

        .step h4 {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .step p {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .step-connector {
            display: none;
        }

        @media (min-width: 992px) {
            .step-connector {
                display: block;
                flex: 0 0 50px;
                height: 2px;
                background: var(--border-color);
                margin-top: 24px;
            }
        }

        /* Features Section */
        .features-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 4rem;
        }

        @media (min-width: 1024px) {
            .features-layout {
                grid-template-columns: 1fr 1fr;
                align-items: center;
            }
        }

        .features-text .title {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .features-text .subtitle {
            font-size: 1.125rem;
            margin-bottom: 3rem;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .feature-list li {
            display: flex;
            gap: 1.25rem;
        }

        .list-icon {
            width: 32px;
            height: 32px;
            background: rgba(0, 178, 89, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 4px;
        }

        .feature-list strong {
            display: block;
            font-size: 1.125rem;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }

        .feature-list p {
            margin: 0;
            font-size: 0.95rem;
        }

        .trust-card-large {
            background: linear-gradient(145deg, var(--bg-card), #f8fafc);
            border: 1px solid var(--border-color);
            padding: 3rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
        }

        .trust-card-header {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(0, 178, 89, 0.1);
            color: var(--color-success);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            background: var(--color-success);
            border-radius: 50%;
            animation: blink 2s infinite;
        }

        .trust-card-large h3 {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .trust-card-large p {
            color: var(--text-body);
            margin-bottom: 2rem;
            font-size: 1.05rem;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.front.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/uat.easytax.live/resources/views/front/pages/home.blade.php ENDPATH**/ ?>