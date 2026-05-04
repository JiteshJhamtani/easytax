<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EasyTax Admin')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/fav3.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { 
            darkMode: 'class',
            corePlugins: {
                preflight: false, // CRITICAL: Stops Tailwind from breaking Bootstrap
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
        .sb-brand img { max-width: 100%; max-height: 50px; object-fit: contain; } /* Slightly smaller for admin */

        .sb-nav { flex: 1; overflow-y: auto; padding: 1rem 0; scrollbar-width: none; }
        .sb-nav::-webkit-scrollbar { display: none; }

        .sb-section {
            padding: 1rem 1.5rem 0.5rem; font-size: 0.7rem; font-weight: 700; 
            letter-spacing: 0.05em; text-transform: uppercase; color: var(--slate-muted); white-space: nowrap;
        }
        .sb-item {
            display: flex; align-items: center; gap: 1rem;
            padding: 0.8rem 1.5rem; margin: 0.2rem 0;
            color: var(--slate-muted); text-decoration: none; font-size: 0.9rem; font-weight: 600;
            transition: var(--t); position: relative; white-space: nowrap;
        }
        .sb-item:hover { color: var(--slate-hi); text-decoration: none; }
        .sb-item.active { background: var(--surface); color: var(--text); border-radius: 0 25px 25px 0; margin-right: 1.5rem; }
        .sb-item.active .sb-item__icon { color: var(--green); opacity: 1; }
        .sb-item__icon { width: 20px; display: flex; justify-content: center; align-items: center; flex-shrink: 0; opacity: 0.8; transition: opacity .2s; }
        .sb-item:hover .sb-item__icon { opacity: 1; }
        .sb-item__dot { width: 8px; height: 8px; background: var(--green); border-radius: 50%; margin-left: auto; flex-shrink: 0; }

        /* ── SUBMENU STYLES ── */
        .sb-has-submenu { flex-direction: column; align-items: stretch; padding: 0; background: transparent; margin: 0; border-radius: 0; }
        .sb-submenu-toggle { display: flex; align-items: center; gap: 1rem; padding: 0.8rem 1.5rem; cursor: pointer; color: var(--slate-muted); font-weight: 600; transition: var(--t); margin: 0.2rem 0; }
        .sb-submenu-toggle:hover { color: var(--slate-hi); }
        .sb-submenu-toggle.active { background: var(--surface); color: var(--text); border-radius: 0 25px 25px 0; margin-right: 1.5rem; }
        .sb-submenu-toggle.active .sb-item__icon { color: var(--green); opacity: 1; }
        .sb-submenu-arrow { margin-left: auto; transition: transform 0.2s; font-size: 0.75rem; }
        .sb-has-submenu.open .sb-submenu-arrow { transform: rotate(180deg); }
        .sb-submenu { display: none; flex-direction: column; background: rgba(0,0,0,0.1); padding: 0.5rem 0; margin-bottom: 0.2rem; }
        .sb-has-submenu.open .sb-submenu { display: flex; }
        .sb-subitem { padding: 0.5rem 1.5rem 0.5rem 3.5rem; color: var(--slate-muted); text-decoration: none; font-size: 0.85rem; font-weight: 600; transition: var(--t); }
        .sb-subitem:hover { color: var(--slate-hi); text-decoration: none; }
        .sb-subitem.active { color: var(--green); }

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
        .namaste-container { position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: default; padding-bottom: 4px; }
        .namaste-text { font-size: 1.4rem; font-weight: 800; color: var(--slate); margin: 0; opacity: 0; animation: slideDownFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; transition: transform 0.3s ease; letter-spacing: -0.01em; }
        .namaste-container:hover .namaste-text { transform: scale(1.02); }
        .namaste-underline { position: absolute; bottom: -6px; left: 0; width: 100%; height: 14px; color: var(--green); overflow: visible; }
        .namaste-underline path { fill: none; stroke: currentColor; stroke-width: 4; stroke-linecap: round; stroke-dasharray: 400; stroke-dashoffset: 400; animation: drawLine 1.5s ease-in-out forwards 0.4s; transition: d 0.8s ease-in-out; d: path("M 0,10 Q 75,0 150,10 Q 225,20 300,10"); vector-effect: non-scaling-stroke; }
        .namaste-container:hover .namaste-underline path { d: path("M 0,10 Q 75,20 150,10 Q 225,0 300,10"); }

        @keyframes slideDownFade { from { opacity: 0; transform: translateY(-15px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes drawLine { to { stroke-dashoffset: 0; } }

        /* ── PREMIUM PROFILE & LOGOUT PILL ── */
        .topbar__actions { display: flex; align-items: center; }
        .user-pill { display: flex; align-items: center; background: var(--surface); border: 1px solid var(--border); border-radius: 50px; padding: 0.3rem 0.3rem 0.3rem 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02); transition: var(--t); }
        .user-pill:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-color: #d1d5db; }
        .user-pill__info { display: flex; flex-direction: column; justify-content: center; margin-right: 1rem; text-decoration: none; }
        .user-pill__name { font-size: 0.8rem; font-weight: 800; color: var(--text); line-height: 1.2; text-decoration: none; }
        .user-pill__role { font-size: 0.65rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
        .user-pill__avatar { width: 38px; height: 38px; border-radius: 50%; background: var(--slate); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 800; margin-right: 0.8rem; border: 2px solid var(--surface); box-shadow: 0 0 0 1px var(--border); }
        .user-pill__divider { width: 1px; height: 24px; background: var(--border); margin: 0 0.5rem; }
        .user-pill__logout { background: none; border: none; color: var(--text-muted); width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; font-size: 1rem; }
        .user-pill__logout:hover { background: #FEE2E2; color: #DC2626; }
        .user-pill__logout svg { width: 18px; height: 18px; }

        /* ── SMOOTH PAGE TRANSITION ── */
        .content-body { flex: 1; padding: 2rem; opacity: 0; animation: pageFadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes pageFadeIn { 0% { opacity: 0; transform: translateY(15px); } 100% { opacity: 1; transform: translateY(0); } }

        /* ── MOBILE & MINI MODE ── */
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
        .shell.sidebar-mini .sidebar { width: var(--sidebar-mini); }
        .shell.sidebar-mini .sb-brand { margin: 1rem 0.5rem; justify-content: center; padding: 0.5rem; }
        .shell.sidebar-mini .sb-brand img { max-height: 24px; }
        .shell.sidebar-mini .sb-section, .shell.sidebar-mini .sb-item__dot, .shell.sidebar-mini .sb-bottom { display: none; }
        .shell.sidebar-mini .sb-item { justify-content: center; padding: 0.8rem; margin: 0.2rem 0.5rem; border-radius: var(--radius); }
        .shell.sidebar-mini .sb-item.active { margin-right: 0.5rem; }
        .shell.sidebar-mini .sb-item__icon { margin: 0; width: auto; }
        .shell.sidebar-mini .main { margin-left: var(--sidebar-mini); }
    </style>
</head>
<body>
<div class="shell" id="shell">

    <aside class="sidebar" id="sidebar">
        <a href="{{ url('admin/dashboard') }}" class="sb-brand">
            <img src="{{ asset('assets/images/logo11.png') }}" alt="EasyTax Logo">
        </a>

<nav class="sb-nav">
            
            {{-- ========================================== --}}
            {{-- ADMIN ONLY SECTION (Hidden from Marketers) --}}
            {{-- ========================================== --}}
            @if(strtoupper(auth()->user()->role) === 'ADMIN')
                <div class="sb-section">Core</div>
                
                <a href="{{ url('admin/dashboard') }}" class="sb-item {{ request()->is('admin/dashboard*') ? 'active' : '' }}" data-label="Dashboard">
                    <span class="sb-item__icon"><i class="fas fa-chart-pie"></i></span>
                    Dashboard
                    @if(request()->is('admin/dashboard*'))<span class="sb-item__dot"></span>@endif
                </a>
                 @if(request()->getHost() == 'b2b.easytax.live')
             
                <div class="sb-section">Management</div>

                <a href="{{ url('admin/services') }}" class="sb-item {{ request()->is('admin/services*') ? 'active' : '' }}" data-label="Services">
                    <span class="sb-item__icon"><i class="fas fa-concierge-bell"></i></span>
                    Services
                    @if(request()->is('admin/services*'))<span class="sb-item__dot"></span>@endif
                </a>
            @endif
                

                <div class="sb-section">Application Types</div>

               

                <a href="{{ route('admin.applications.index', ['type' => 'itr-filing']) }}" class="sb-item {{ request()->query('type') === 'itr-filing' ? 'active' : '' }}" data-label="ITR Filing">
                    <span class="sb-item__icon"><i class="fas fa-file-invoice-dollar" style="color: var(--green);"></i></span>
                    ITR Filing
                </a>

                <a href="{{ route('admin.applications.index', ['type' => 'gst-registration']) }}" class="sb-item {{ request()->query('type') === 'gst-registration' ? 'active' : '' }}" data-label="GST Registration">
                    <span class="sb-item__icon"><i class="fas fa-id-card" style="color: #fbbf24;"></i></span>
                    GST Registration
                </a>
                 <a href="{{ route('admin.applications.index', ['type' => 'gst-return-filing']) }}" class="sb-item {{ request()->query('type') === 'gst-return-filing' ? 'active' : '' }}" data-label="GST Return">
                    <span class="sb-item__icon"><i class="fas fa-file-invoice" style="color: #38bdf8;"></i></span>
                    GST Return
                </a>

                <a href="{{ route('admin.applications.index', ['type' => 'other']) }}" class="sb-item {{ request()->query('type', 'other') === 'other' ? 'active' : '' }}" data-label="Other Apps">
                    <span class="sb-item__icon"><i class="fas fa-folder" style="color: var(--slate-muted);"></i></span>
                    Other Apps
                </a>
                
                <a href="{{ route('admin.applications.index', ['type' => 'incomplete']) }}" class="sb-item {{ request()->query('type') === 'incomplete' ? 'active' : '' }}" data-label="Incomplete Apps">
                    <span class="sb-item__icon"><i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i></span>
                    Incomplete Apps
                </a>
                
                <a href="{{ url('admin/agents') }}" class="sb-item {{ request()->is('admin/agents*') ? 'active' : '' }}" data-label="Agents">
                    <span class="sb-item__icon"><i class="fas fa-user-tie"></i></span>
                    Agents
                    @if(request()->is('admin/agents*'))<span class="sb-item__dot"></span>@endif
                </a>
 @if(request()->getHost() == 'b2b.easytax.live')
                <div class="sb-section">System</div>

                <div class="sb-has-submenu {{ request()->is('admin/gifts*') ? 'open' : '' }}">
                    <div class="sb-submenu-toggle {{ request()->is('admin/gifts*') ? 'active' : '' }}" onclick="this.parentElement.classList.toggle('open')">
                        <span class="sb-item__icon"><i class="fas fa-gift"></i></span>
                        Gifts
                        <i class="fas fa-chevron-down sb-submenu-arrow"></i>
                    </div>
                    <div class="sb-submenu">
                        <a href="{{ url('admin/gifts') }}" class="sb-subitem {{ request()->routeIs('admin.gifts.index') ? 'active' : '' }}">All Gifts</a>
                        <a href="{{ url('admin/gifts/eligibility') }}" class="sb-subitem {{ request()->routeIs('admin.gifts.eligibility*') ? 'active' : '' }}">Eligibility</a>
                    </div>
                </div>

                
              

                <a href="{{ url('admin/pages') }}" class="sb-item {{ request()->is('admin/pages*') ? 'active' : '' }}" data-label="Pages">
                    <span class="sb-item__icon"><i class="fas fa-file-alt"></i></span>
                    Pages
                    @if(request()->is('admin/pages*'))<span class="sb-item__dot"></span>@endif
                </a>
                
@endif

            @endif

            {{-- ========================================== --}}
            {{-- CRM SECTION (Visible to Admin & Marketers) --}}
            {{-- ========================================== --}}
            <div class="sb-section">Marketing CRM</div>

            {{-- Only Admins can manage the actual Marketer accounts --}}
            @if(strtoupper(auth()->user()->role) === 'ADMIN')
                <a href="{{ route('crm.marketers.index') }}" class="sb-item {{ request()->is('crm/marketers*') ? 'active' : '' }}" data-label="Marketers">
                    <span class="sb-item__icon"><i class="fas fa-bullhorn" style="color: #f43f5e;"></i></span>
                    Marketers
                    @if(request()->is('crm/marketers*'))<span class="sb-item__dot"></span>@endif
                </a>
            @endif

            {{-- Both Admins and Marketers can access Leads --}}
            <a href="{{ route('crm.leads.index') }}" class="sb-item {{ request()->is('crm/leads*') ? 'active' : '' }}" data-label="Leads">
                <span class="sb-item__icon"><i class="fas fa-magnet" style="color: #8b5cf6;"></i></span>
                Leads
                @if(request()->is('crm/leads*'))<span class="sb-item__dot"></span>@endif
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
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button class="topbar__toggle" id="sb-toggle">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
                
                <div class="namaste-container">
                    <h2 class="namaste-text">Namaste, {{ explode(' ', Auth::user()->name ?? 'Admin')[0] }}!</h2>
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
                        <span class="user-pill__name">{{ Auth::user()->name ?? 'Super Admin' }}</span>
                        <span class="user-pill__role">Super Admin</span>
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
            {{-- Backward compatibility for AdminLTE pages --}}
            @yield('content_header') 
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
</body>
</html>