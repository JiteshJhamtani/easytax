@props(['sessions', 'currentSessionLabel'])

<style>
    .session-switcher-btn {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        color: #0f172a;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 0.55rem 1.1rem;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .session-switcher-btn:hover, .session-switcher-btn[aria-expanded="true"] {
        background-color: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
        text-decoration: none;
    }
    .session-switcher-btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15); /* Brand green focus ring */
        border-color: #16a34a;
    }
    .session-switcher-btn i.fa-calendar-alt {
        color: #16a34a; /* Theme Green */
        font-size: 1.05rem;
    }
    .session-switcher-btn i.fa-chevron-down {
        color: #64748b;
        font-size: 0.7rem;
        margin-left: 0.25rem;
        transition: transform 0.2s;
    }
    .dropdown.show .session-switcher-btn i.fa-chevron-down {
        transform: rotate(180deg);
    }
    
    .session-dropdown-menu {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        padding: 0.5rem;
        min-width: 240px;
        margin-top: 0.5rem !important;
    }
    .session-dropdown-item {
        padding: 0.6rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.15s;
        margin-bottom: 2px;
    }
    .session-dropdown-item:last-child {
        margin-bottom: 0;
    }
    .session-dropdown-item:hover, .session-dropdown-item:focus {
        background-color: #f1f5f9;
        color: #0f172a;
        text-decoration: none;
        outline: none;
    }
    .session-dropdown-item.active {
        background-color: #f0fdf4; /* Light green tint */
        color: #166534; /* Dark green text */
    }
    .session-dropdown-item.active .check-icon {
        display: block;
    }
    .session-dropdown-item .check-icon {
        display: none;
        color: #16a34a;
    }
</style>

<div class="dropdown d-inline-block">
    <button class="session-switcher-btn" type="button" id="sessionDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="far fa-calendar-alt"></i> 
        <span>Session: {{ $currentSessionLabel }}</span>
        <i class="fas fa-chevron-down"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-right session-dropdown-menu animate fadeIn" aria-labelledby="sessionDropdown">
        <div class="px-3 py-2 mb-1 text-uppercase text-muted" style="font-size: 0.68rem; font-weight: 800; letter-spacing: 0.05em;">
            Select Financial Cycle
        </div>
        @foreach($sessions as $session)
            <a class="dropdown-item session-dropdown-item {{ $session['label'] === $currentSessionLabel ? 'active' : '' }}" 
               href="{{ request()->fullUrlWithQuery(['session' => $session['label'], 'page' => 1]) }}">
                <span>{{ $session['name'] }}</span>
                <i class="fas fa-check check-icon"></i>
            </a>
        @endforeach
    </div>
</div>
