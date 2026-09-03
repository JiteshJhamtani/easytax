<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EasyTax Agent')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { 
            darkMode: 'class',
            corePlugins: {
                preflight: false, // CRITICAL: This stops Tailwind from breaking your Bootstrap Sidebar! 
            }
        }
    </script>
    
    @yield('css')

    <style>
        :root {
            /* Color Palette */
            --green:        #1E9C5D; 
            --green-dark:   #157a48;
            --green-light:  #EDF7F4; 
            --slate:        #2E3D4E; 
            --slate-dark:   #1f2a36;
            --slate-border: rgba(255,255,255,.06);
            --slate-muted:  rgba(255,255,255,.6);
            --slate-hi:     #ffffff;
            --bg:           #F8F9FA; 
            --surface:      #ffffff;
            --text:         #333333; 
            --text-muted:   #7a8799;
            --border:       #e8ecf0;
            --ink-100:      #f1f5f9;
            
            /* Sizing & Effects */
            --sidebar-w:    260px;
            --sidebar-mini: 76px;
            --topbar-h:     76px;
            --radius:       12px;
            --shadow:       0 4px 12px rgba(0,0,0,.04);
            --t:            all .22s cubic-bezier(.4,0,.2,1);
        }

        [x-cloak] { display: none !important; }

        *, *::before, *::after { box-sizing: border-box; }
        html, body {
            margin: 0; padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px; background: var(--bg); color: var(--text); height: 100%;
        }

        .shell { display: flex; min-height: 100vh; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w); background: var(--slate);
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; bottom: 0;
            z-index: 1000; flex-shrink: 0;
            transition: transform .28s cubic-bezier(.4,0,.2,1), width .22s cubic-bezier(.4,0,.2,1);
            overflow: hidden;
        }

        .sb-brand {
            display: flex; align-items: center; justify-content: center;
            margin: 1.5rem 1.5rem 1rem; 
            padding: 1rem; 
            background: var(--surface); 
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-decoration: none; flex-shrink: 0;
            transition: var(--t);
        }
        .sb-brand:hover { text-decoration: none; transform: translateY(-1px); }
        .sb-brand img { max-width: 100%; max-height: 60px; object-fit: contain; }

        .sb-nav { flex: 1; overflow-y: auto; padding: 1rem 0; scrollbar-width: none; }
        .sb-nav::-webkit-scrollbar { display: none; }

        .sb-section {
            padding: 1rem 1.5rem 0.5rem; font-size: 0.7rem; font-weight: 700; 
            letter-spacing: 0.05em; text-transform: uppercase; color: var(--slate-muted); 
        }
        .sb-item {
            display: flex; align-items: center; gap: 1rem;
            padding: 0.8rem 1.5rem; margin: 0.2rem 0;
            color: var(--slate-muted); text-decoration: none; font-size: 0.9rem; font-weight: 600;
            transition: var(--t); position: relative; 
        }
        .sb-item:hover { color: var(--slate-hi); text-decoration: none; }
        .sb-item.active { background: var(--surface); color: var(--text); border-radius: 0 25px 25px 0; margin-right: 1.5rem; }
        .sb-item.active .sb-item__icon { color: var(--green); opacity: 1; }
        .sb-item__icon { width: 20px; display: flex; justify-content: center; align-items: center; flex-shrink: 0; opacity: 0.8; transition: opacity .2s; }
        .sb-item__dot { width: 8px; height: 8px; background: var(--green); border-radius: 50%; margin-left: auto; flex-shrink: 0; }
        .sb-item__label { flex: 1; min-width: 0; }

        /* ── SIDEBAR NOTIFICATION BADGES (CIRCLES) ── */
        .sb-badge-cluster { display: inline-flex; align-items: center; gap: 0.35rem; margin-left: auto; flex-shrink: 0; }
        .sb-badge {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 20px; height: 20px; padding: 0 5.5px; border-radius: 9999px;
            font-size: 0.72rem; font-weight: 800; line-height: 1; letter-spacing: -0.01em;
            color: #ffffff !important; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.35);
            transition: transform 0.15s ease; cursor: default; user-select: none;
        }
        .sb-item:hover .sb-badge { transform: scale(1.08); }
        .sb-badge--red    { background-color: #ef4444 !important; color: #ffffff !important; }
        .sb-badge--blue   { background-color: #3b82f6 !important; color: #ffffff !important; }
        .sb-badge--amber  { background-color: #f97316 !important; color: #ffffff !important; }
        .sb-badge--green  { background-color: #10b981 !important; color: #ffffff !important; }
        .sb-badge--purple { background-color: #8b5cf6 !important; color: #ffffff !important; }
        .sb-badge--pink   { background-color: #ec4899 !important; color: #ffffff !important; }
        .sb-badge--teal   { background-color: #06b6d4 !important; color: #ffffff !important; }
        .sb-badge--indigo { background-color: #6366f1 !important; color: #ffffff !important; }
        .sb-badge--slate  { background-color: #475569 !important; color: #ffffff !important; }

        .sb-bottom { padding: 1rem 1.5rem 1.5rem; flex-shrink: 0; display: flex; flex-direction: column; gap: 1rem; margin-top: auto; }
        .sb-illustration { text-align: center; width: 100%; display: flex; align-items: center; justify-content: center; }
        .sb-illustration svg { max-width: 100%; height: auto; display: block; margin: 0 auto; }

        /* ── MAIN ── */
        .main { flex: 1; margin-left: var(--sidebar-w); display: flex; flex-direction: column; min-width: 0; transition: margin-left .28s cubic-bezier(.4,0,.2,1); }

        .topbar {
            height: var(--topbar-h); background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between; padding: 0 2.5rem; gap: 1rem;
            position: sticky; top: 0; z-index: 900;
        }
        .topbar__toggle { display: none; background: none; border: none; padding: .3rem .45rem; cursor: pointer; color: var(--text-muted); font-size: 17px; border-radius: 6px; transition: background .2s; }
        .topbar__toggle:hover { background: var(--ink-100); }

        /* ── NAMASTE ANIMATION ── */
        .namaste-container {
            position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center;
            cursor: default; padding-bottom: 4px;
        }
        .namaste-text {
            font-size: 1.4rem; font-weight: 800; color: var(--slate); margin: 0; opacity: 0;
            animation: slideDownFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transition: transform 0.3s ease; letter-spacing: -0.01em;
        }
        .namaste-container:hover .namaste-text { transform: scale(1.02); }
        .namaste-underline { position: absolute; bottom: -6px; left: 0; width: 100%; height: 14px; color: var(--green); overflow: visible; }
        .namaste-underline path {
            fill: none; stroke: currentColor; stroke-width: 4; stroke-linecap: round;
            stroke-dasharray: 400; stroke-dashoffset: 400;
            animation: drawLine 1.5s ease-in-out forwards 0.4s; transition: d 0.8s ease-in-out;
            d: path("M 0,10 Q 75,0 150,10 Q 225,20 300,10"); vector-effect: non-scaling-stroke;
        }
        .namaste-container:hover .namaste-underline path { d: path("M 0,10 Q 75,20 150,10 Q 225,0 300,10"); }

        @keyframes slideDownFade {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes drawLine { to { stroke-dashoffset: 0; } }

        /* ── PREMIUM PROFILE & LOGOUT PILL ── */
        .topbar__actions { display: flex; align-items: center; }
        .user-pill {
            display: flex; align-items: center; background: var(--surface); border: 1px solid var(--border);
            border-radius: 50px; padding: 0.3rem 0.3rem 0.3rem 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02); transition: var(--t);
        }
        .user-pill:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-color: #d1d5db; }
        .user-pill__info { display: flex; flex-direction: column; justify-content: center; margin-right: 1rem; text-decoration: none; }
        .user-pill__name { font-size: 0.8rem; font-weight: 800; color: var(--text); line-height: 1.2; text-decoration: none; }
        .user-pill__role { font-size: 0.65rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
        a.user-pill__info:hover .user-pill__name { color: var(--green); }
        .user-pill__avatar {
            width: 38px; height: 38px; border-radius: 50%; background: var(--green-light); color: var(--green);
            display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 800;
            margin-right: 0.8rem; border: 2px solid var(--surface); box-shadow: 0 0 0 1px var(--border);
        }
        .user-pill__divider { width: 1px; height: 24px; background: var(--border); margin: 0 0.5rem; }
        .user-pill__logout {
            background: none; border: none; color: var(--text-muted); width: 38px; height: 38px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; font-size: 1rem;
        }
        .user-pill__logout:hover { background: #FEE2E2; color: #DC2626; }
        .user-pill__logout svg { width: 18px; height: 18px; }

        /* ── SMOOTH PAGE TRANSITION ── */
        .content-body { 
            flex: 1; padding: 2rem;
        }

        /* ── GLOBAL RESPONSIVE DATA-TABLES ── */
        table.modern-responsive-table {
            border-collapse: collapse !important;
            width: 100% !important; 
            margin-bottom: 0 !important;
        }
        table.modern-responsive-table thead th {
            
        }
        table.modern-responsive-table tbody td {
            white-space: normal !important; 
            word-break: break-word !important; 
            padding: 1rem 0.75rem !important;
            vertical-align: middle;
        }
        /* Protect the Action Column on Desktop */
        table.modern-responsive-table th:last-child,
        table.modern-responsive-table td:last-child {
            min-width: 100px;
            
        }

        /* ── MOBILE ── */
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); box-shadow: 8px 0 32px rgba(0,0,0,.28); }
            .main { margin-left: 0; }
            .topbar { padding: 0 1.5rem; }
            .topbar__toggle { display: flex; align-items: center; justify-content: center; }
            .content-body { padding: 1rem; }
            .sb-overlay { display: none; position: fixed; inset: 0; background: rgba(46,61,78,.6); z-index: 999; backdrop-filter: blur(2px); }
            .sb-overlay.open { display: block; }
            .user-pill__info { display: none; } 
            .namaste-text { font-size: 1.1rem; }
        }

        

        /* ── EXTRA SMALL MOBILE TWEAKS (Phones < 576px) ── */
        @media (max-width: 575px) {
            .topbar { padding: 0 1rem; gap: 0.5rem; }
            .namaste-text { font-size: 0.95rem; }
            .user-pill { padding: 0.2rem 0.2rem 0.2rem 0.5rem; }
            .user-pill__avatar { width: 32px; height: 32px; font-size: 0.8rem; margin-right: 0.5rem; }
            .user-pill__logout { width: 32px; height: 32px; }
            .user-pill__divider { margin: 0 0.3rem; }
            .content-body { padding: 0.75rem; }
        }

        /* ── MINI MODE ── */
        .shell.sidebar-mini .sidebar { width: var(--sidebar-mini); }
        .shell.sidebar-mini .sb-brand { margin: 1rem 0.5rem; justify-content: center; padding: 0.5rem; }
        .shell.sidebar-mini .sb-brand img { max-height: 24px; }
        .shell.sidebar-mini .sb-section, .shell.sidebar-mini .sb-item__dot, .shell.sidebar-mini .sb-bottom, .shell.sidebar-mini .sb-item__label, .shell.sidebar-mini .sb-badge-cluster { display: none; }
        .shell.sidebar-mini .sb-item { justify-content: center; padding: 0.8rem; margin: 0.2rem 0.5rem; border-radius: var(--radius); }
        .shell.sidebar-mini .sb-item.active { margin-right: 0.5rem; }
        .shell.sidebar-mini .sb-item__icon { margin: 0; width: auto; }
        .shell.sidebar-mini .main { margin-left: var(--sidebar-mini); }
        .shell.sidebar-mini .sb-item::after { content: attr(data-label); position: absolute; left: calc(100% + 10px); top: 50%; transform: translateY(-50%); background: var(--slate-dark); color: #fff; font-size: 0.75rem; font-weight: 600; padding: 0.4rem 0.8rem; border-radius: 6px;  opacity: 0; pointer-events: none; transition: opacity .15s; z-index: 9999; box-shadow: var(--shadow); }
        .shell.sidebar-mini .sb-item:hover::after { opacity: 1; }
        @livewireStyles
    </style>
</head>
<body>
<div class="shell" id="shell">

    <aside class="sidebar" id="sidebar">
        <a href="{{ url('agent/dashboard') }}" class="sb-brand">
            <img src="{{ asset('assets/images/logo11.png') }}" alt="EasyTax Logo">
        </a>

        <nav class="sb-nav">
            <div class="sb-section">Applications</div>
            
            <a href="{{ url('agent/dashboard') }}" wire:navigate class="sb-item {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}" data-label="Dashboard">
                <span class="sb-item__icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                </span>
                Dashboard
                @if(request()->is('agent/dashboard*'))<span class="sb-item__dot"></span>@endif
            </a>
            
            
            <a href="{{ url('services') }}" wire:navigate class="sb-item {{ request()->is('services') ? 'active' : '' }}" data-label="More Applications">
                <span class="sb-item__icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                </span>
                More Applications
            </a> 
       <div class="sb-section">Application Types</div>


            <a href="{{ route('agent.applications.index', ['type' => 'itr-filing']) }}" wire:navigate class="sb-item {{ request()->query('type') === 'itr-filing' ? 'active' : '' }}" data-label="ITR Filing">
                <span class="sb-item__icon"><i class="fas fa-file-invoice-dollar"></i></span>
                <span class="sb-item__label">ITR Filing</span>
                @include('layouts.partials.sidebar-tab-badges', ['tabKey' => 'itr-filing'])
            </a>

            <a href="{{ route('agent.applications.index', ['type' => 'gst-registration']) }}" wire:navigate class="sb-item {{ request()->query('type') === 'gst-registration' ? 'active' : '' }}" data-label="GST Registration">
                <span class="sb-item__icon"><i class="fas fa-id-card"></i></span>
                <span class="sb-item__label">GST Registration</span>
                @include('layouts.partials.sidebar-tab-badges', ['tabKey' => 'gst-registration'])
            </a>
            
            <a href="{{ route('agent.applications.index', ['type' => 'gst-return-filing']) }}" wire:navigate class="sb-item {{ request()->query('type') === 'gst-return-filing' ? 'active' : '' }}" data-label="GST Return">
                <span class="sb-item__icon"><i class="fas fa-file-invoice"></i></span>
                <span class="sb-item__label">GST Return</span>
                @include('layouts.partials.sidebar-tab-badges', ['tabKey' => 'gst-return-filing'])
            </a>

            <a href="{{ route('agent.applications.index', ['type' => 'other']) }}" wire:navigate class="sb-item {{ request()->query('type', 'other') === 'other' ? 'active' : '' }}" data-label="Other Apps">
                <span class="sb-item__icon"><i class="fas fa-folder"></i></span>
                <span class="sb-item__label">Other Apps</span>
                @include('layouts.partials.sidebar-tab-badges', ['tabKey' => 'other'])
            </a>
        </nav>

        <div class="sb-bottom">
            <div class="sb-illustration">
                @includeIf('svg.' . strtolower(date('l')))
            </div>
        </div>
    </aside>

    <div class="sb-overlay" id="sb-overlay"></div>

    <div class="main" id="main">
        <header class="topbar">
            <div style="display: flex; align-items: center; gap: 1rem; min-width: 0;">
                <button class="topbar__toggle" id="sb-toggle">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
                
                <div class="namaste-container">
                    <h2 class="namaste-text">Namaste, {{ explode(' ', Auth::user()->name ?? 'Agent')[0] }}!</h2>
                    <svg class="namaste-underline" viewBox="0 0 300 20" preserveAspectRatio="none">
                        <path d="M 0,10 Q 75,0 150,10 Q 225,20 300,10" />
                    </svg>
                </div>
            </div>

            <div class="topbar__actions">
                <div class="user-pill">
                    <div class="user-pill__avatar">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    
                    <a href="{{ route('profile.edit') }}" class="user-pill__info">
                        <span class="user-pill__name">{{ Auth::user()->name ?? 'Agent' }}</span>
                        <span class="user-pill__role">Workspace</span>
                    </a>
                    
                    <div class="user-pill__divider"></div>
                    
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="user-pill__logout" title="Sign out">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="content-body">
            @yield('content')
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
    const shell=document.getElementById('shell'),sidebar=document.getElementById('sidebar'),overlay=document.getElementById('sb-overlay'),mBtn=document.getElementById('sb-toggle');
    mBtn&&mBtn.addEventListener('click',()=>{sidebar.classList.toggle('open');overlay.classList.toggle('open');});
    overlay&&overlay.addEventListener('click',()=>{sidebar.classList.remove('open');overlay.classList.remove('open');});
    if(localStorage.getItem('et_sb_mini')==='1')shell.classList.add('sidebar-mini');
})();
</script>
@yield('js')

<!-- GLOBAL RESPONSIVE TABLE SCRIPT -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function applyTableLabels() {
            document.querySelectorAll('table.responsive-card-table').forEach(function(table) {
                var headers = [];
                table.querySelectorAll('thead th').forEach(function(th) {
                    headers.push(th.textContent.trim());
                });
                
                function applyLabels() {
                    table.querySelectorAll('tbody tr').forEach(function(tr) {
                        tr.querySelectorAll('td').forEach(function(td, index) {
                            if(headers[index] && !td.hasAttribute('data-label')) {
                                td.setAttribute('data-label', headers[index]);
                            }
                        });
                    });
                }
                
                applyLabels();
                
                if (window.jQuery && $(table).hasClass('dataTable')) {
                    $(table).on('draw.dt', applyLabels);
                }
            });
        }
        
        applyTableLabels();
        
        if (window.jQuery) {
            $(document).on('init.dt', function() {
                applyTableLabels();
            });
        }
    });
</script>
<!-- GLOBAL ACTION CONFIRMATION MODAL -->
<div x-data="{ 
        open: false, 
        formElement: null,
        title: 'Are you sure?',
        message: 'This action cannot be undone.',
        confirmText: 'Confirm',
        confirmColor: 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
     }" 
     @confirm-action.window="
        open = true; 
        formElement = $event.detail.form;
        if($event.detail.title) title = $event.detail.title;
        if($event.detail.message) message = $event.detail.message;
        if($event.detail.confirmText) confirmText = $event.detail.confirmText;
        if($event.detail.confirmColor) confirmColor = $event.detail.confirmColor;
     "
     @keydown.escape.window="open = false"
     class="relative z-[9999]"
     x-cloak
     x-show="open">
    
    <!-- Backdrop Overlay -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" 
         @click="open = false"></div>

    <!-- Modal Panel Container -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100">
                
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="flex flex-col items-center">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 mb-4">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title" x-text="title"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-text="message"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 flex justify-center gap-3 sm:px-6 border-t border-gray-100">
                    <button type="button" 
                            @click="open = false" 
                            class="inline-flex w-full justify-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:w-auto">Cancel</button>
                    <button type="button" 
                            @click="formElement ? formElement.submit() : open = false" 
                            :class="'inline-flex w-full justify-center rounded-lg px-4 py-2 text-sm font-semibold text-white shadow-sm sm:w-auto ' + confirmColor"
                            x-text="confirmText"></button>
                </div>
            </div>
        </div>
    </div>
</div>
@livewireScripts
</body>
</html>