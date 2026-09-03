@php
    $user = auth()->user();
    $userRole = strtoupper($user->role ?? '');
    if (in_array($userRole, ['ADMIN', 'SUPER_ADMIN', 'SUB-ADMIN', 'TEAM'])) {
        $layout = 'layouts.admin';
    } elseif ($userRole === 'MARKETER') {
        $layout = 'layouts.marketer';
    } else {
        $layout = 'layouts.agent';
    }
@endphp

@extends($layout)

@section('title', 'Account Profile & Settings | EasyTax')

@section('content')
<div class="container-fluid px-0 px-md-2">
    
    {{-- Top Breadcrumb & Actions --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 font-weight-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fas fa-user-cog text-success mr-2"></i> Account Profile & Settings
            </h1>
            <p class="text-muted mb-0 small">
                Manage your credentials, personal contact details, and account preferences.
            </p>
        </div>
        <div class="mt-2 mt-sm-0">
            <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary font-weight-bold px-3 py-2 rounded-lg">
                <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    {{-- Status Alerts --}}
    @if (session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show rounded-xl border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
            <i class="fas fa-check-circle text-success fs-5 mr-2"></i>
            <div>
                <strong>Success!</strong> Your personal profile and details have been updated successfully.
            </div>
            <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="alert alert-success alert-dismissible fade show rounded-xl border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
            <i class="fas fa-shield-alt text-success fs-5 mr-2"></i>
            <div>
                <strong>Security Updated!</strong> Your account password has been updated securely.
            </div>
            <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Profile Hero Overview Card --}}
    <div class="et-card et-hero-card mb-4">
        <div class="et-hero-bg-accent"></div>
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between p-4 p-md-4 gap-4 position-relative z-10">
            <div class="d-flex align-items-center gap-3">
                {{-- Avatar Initial Circle --}}
                <div class="et-avatar-pill">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <h2 class="h4 font-weight-bold text-dark mb-0">{{ $user->name }}</h2>
                        
                        {{-- Role Badge --}}
                        @if($user->isAdmin())
                            <span class="custom-badge badge-admin"><i class="fas fa-user-shield mr-1"></i> Administrator</span>
                        @elseif($userRole === 'MARKETER')
                            <span class="custom-badge badge-marketer"><i class="fas fa-bullhorn mr-1"></i> Marketing Partner</span>
                        @elseif($userRole === 'TEAM')
                            <span class="custom-badge badge-team"><i class="fas fa-headset mr-1"></i> Operations Team</span>
                        @else
                            <span class="custom-badge badge-agent"><i class="fas fa-user-tie mr-1"></i> EasyTax Agent</span>
                        @endif

                        {{-- Agent Code --}}
                        @if(!empty($user->agent_code))
                            <span class="agent-code-tag" title="Agent Referral Code">
                                <i class="fas fa-id-badge text-muted mr-1"></i> {{ $user->agent_code }}
                            </span>
                        @endif

                        {{-- Status Pill --}}
                        <span class="custom-badge badge-success-soft">
                            <i class="fas fa-check-circle mr-1"></i> Active
                        </span>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-3 mt-2 text-muted small">
                        <span><i class="fas fa-envelope text-success mr-1"></i> {{ $user->email }}</span>
                        @if(!empty($user->mobile_number))
                            <span><i class="fas fa-phone text-primary mr-1"></i> {{ $user->mobile_number }}</span>
                        @endif
                        @if(!empty($user->whatsapp_no))
                            <span><i class="fab fa-whatsapp text-success mr-1"></i> {{ $user->whatsapp_no }}</span>
                        @endif
                        <span><i class="fas fa-calendar-alt text-info mr-1"></i> Joined {{ $user->created_at?->format('M Y') ?? 'Recently' }}</span>
                    </div>
                </div>
            </div>

            <div class="et-hero-actions text-md-right">
                <div class="text-xs text-muted font-weight-bold text-uppercase tracking-wider">Account ID</div>
                <div class="font-weight-bold text-dark fs-6">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>
    </div>

    {{-- Main Form Grid --}}
    <div class="row">
        
        {{-- Left Column (Personal Info & Notifications) --}}
        <div class="col-lg-7 mb-4">
            
            {{-- Profile Information Card --}}
            <div class="et-card mb-4">
                <div class="et-card-header">
                    <div class="et-card-icon bg-success-soft text-success">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div>
                        <h3 class="et-card-title">Personal Information</h3>
                        <p class="et-card-subtitle">Update your official contact details and profile address.</p>
                    </div>
                </div>
                <div class="et-card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Notification Preferences Card (Admins / Operations) --}}
            @if($user->isAdmin() || in_array($userRole, ['ADMIN', 'SUPER_ADMIN', 'SUB-ADMIN', 'TEAM']))
            <div class="et-card mb-4">
                <div class="et-card-header">
                    <div class="et-card-icon bg-warning-soft text-warning">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <h3 class="et-card-title">Notification Settings</h3>
                        <p class="et-card-subtitle">Choose how EasyTax dispatches alerts for client applications and payments.</p>
                    </div>
                </div>
                <div class="et-card-body">
                    @include('profile.partials.update-notification-preference-form')
                </div>
            </div>
            @endif

        </div>

        {{-- Right Column (Password & Danger Zone) --}}
        <div class="col-lg-5 mb-4">
            
            {{-- Security / Password Card --}}
            <div class="et-card mb-4">
                <div class="et-card-header">
                    <div class="et-card-icon bg-primary-soft text-primary">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h3 class="et-card-title">Security & Password</h3>
                        <p class="et-card-subtitle">Ensure your account is protected with a strong credentials password.</p>
                    </div>
                </div>
                <div class="et-card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Account Deletion / Danger Zone --}}
            <div class="et-card et-card-danger mb-4">
                <div class="et-card-header border-bottom border-red-100 bg-red-50/40">
                    <div class="et-card-icon bg-danger-soft text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h3 class="et-card-title text-danger">Account Deactivation</h3>
                        <p class="et-card-subtitle text-danger opacity-75">Close your EasyTax profile and revoke active session tokens.</p>
                    </div>
                </div>
                <div class="et-card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@section('css')
<style>
    /* ── EASYTAX THEME SYSTEM ── */
    :root {
        --et-green: #1E9C5D;
        --et-green-dark: #157a48;
        --et-green-light: #EDF7F4;
        --et-slate: #2E3D4E;
        --et-slate-dark: #1f2a36;
        --et-bg: #F8F9FA;
        --et-border: #E8ECF0;
    }

    /* Cards */
    .et-card {
        background: #ffffff;
        border: 1px solid #E8ECF0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .et-card:hover {
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.05);
    }

    .et-card-danger {
        border-color: #FECACA !important;
    }

    /* Card Header */
    .et-card-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #F1F5F9;
        background: #ffffff;
    }

    .et-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .et-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1E293B;
        margin-bottom: 0.15rem;
    }

    .et-card-subtitle {
        font-size: 0.8rem;
        color: #64748B;
        margin-bottom: 0;
    }

    .et-card-body {
        padding: 1.75rem;
    }

    /* Hero Card */
    .et-hero-card {
        position: relative;
        background: linear-gradient(135deg, #ffffff 0%, #F8FAFC 100%);
        border: 1px solid #E2E8F0;
    }

    .et-hero-bg-accent {
        position: absolute;
        top: 0;
        right: 0;
        width: 320px;
        height: 100%;
        background: radial-gradient(circle, rgba(30, 156, 93, 0.08) 0%, rgba(255, 255, 255, 0) 70%);
        pointer-events: none;
    }

    .et-avatar-pill {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1E9C5D 0%, #10B981 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        font-weight: 800;
        box-shadow: 0 6px 16px rgba(30, 156, 93, 0.25);
        border: 3px solid #ffffff;
        flex-shrink: 0;
    }

    /* Badges */
    .custom-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.3rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .badge-admin { background: #1f2a36; color: #ffffff; }
    .badge-agent { background: #1E9C5D; color: #ffffff; }
    .badge-marketer { background: #4F46E5; color: #ffffff; }
    .badge-team { background: #0284C7; color: #ffffff; }

    .badge-success-soft { background: #EDF7F4; color: #157a48; }
    .badge-info-soft { background: #E0F2FE; color: #0369A1; }
    .badge-warning-soft { background: #FEF3C7; color: #B45309; }
    .badge-danger-soft { background: #FEE2E2; color: #DC2626; }

    .bg-success-soft { background: #EDF7F4; }
    .bg-info-soft { background: #E0F2FE; }
    .bg-warning-soft { background: #FEF3C7; }
    .bg-danger-soft { background: #FEE2E2; }
    .bg-primary-soft { background: #EEF2FF; }

    .agent-code-tag {
        display: inline-flex;
        align-items: center;
        background: #F1F5F9;
        color: #1E293B;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-family: monospace;
        font-weight: 700;
        font-size: 0.85rem;
        border: 1px solid #CBD5E1;
    }

    /* Form Controls */
    .et-form-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #475569;
        margin-bottom: 0.45rem;
        letter-spacing: 0.5px;
        display: block;
    }

    .et-input-group {
        display: flex;
        align-items: center;
        border: 1px solid #CBD5E1;
        border-radius: 10px;
        background: #ffffff;
        transition: border-color 0.2s, box-shadow 0.2s;
        overflow: hidden;
    }

    .et-input-group:focus-within {
        border-color: #1E9C5D;
        box-shadow: 0 0 0 3px rgba(30, 156, 93, 0.15);
    }

    .et-input-icon {
        width: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94A3B8;
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    .et-input {
        flex: 1;
        border: none;
        outline: none;
        height: 44px;
        padding: 0.5rem 0.85rem;
        font-size: 0.95rem;
        color: #1E293B;
        background: transparent;
    }

    .et-input.et-textarea {
        height: auto;
        min-height: 80px;
        resize: vertical;
    }

    .et-password-toggle {
        background: none;
        border: none;
        padding: 0 1rem;
        height: 44px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        outline: none;
    }

    .et-password-toggle:hover i {
        color: #1E293B !important;
    }

    .et-error-msg {
        display: block;
        color: #DC2626;
        font-size: 0.8rem;
        font-weight: 600;
        margin-top: 0.35rem;
    }

    /* Buttons */
    .et-btn {
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 0.65rem 1.4rem;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .et-btn:hover {
        text-decoration: none;
        transform: translateY(-1px);
    }

    .et-btn-primary {
        background: #1E9C5D;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(30, 156, 93, 0.25);
    }

    .et-btn-primary:hover {
        background: #157a48;
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(30, 156, 93, 0.35);
    }

    .et-btn-slate {
        background: #2E3D4E;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(46, 61, 78, 0.25);
    }

    .et-btn-slate:hover {
        background: #1f2a36;
        color: #ffffff;
    }

    .et-btn-danger {
        background: #DC2626;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
    }

    .et-btn-danger:hover {
        background: #B91C1C;
        color: #ffffff;
    }

    /* Radio Cards */
    .et-radio-card {
        display: block;
        border: 1.5px solid #E2E8F0;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        cursor: pointer;
        background: #ffffff;
        transition: all 0.2s ease;
    }

    .et-radio-card:hover {
        border-color: #CBD5E1;
        background: #F8FAFC;
    }

    .et-radio-card.active {
        border-color: #1E9C5D;
        background: #F0FDF4;
        box-shadow: 0 0 0 2px rgba(30, 156, 93, 0.15);
    }

    .et-status-pill {
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
    }
</style>
@endsection

@section('js')
<script>
    // Password visibility toggle
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (!input || !icon) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Radio card active state switcher
    function updateRadioCardStyles(selectedRadio) {
        const container = selectedRadio.closest('.et-radio-cards');
        if (!container) return;

        container.querySelectorAll('.et-radio-card').forEach(card => {
            card.classList.remove('active');
        });

        const activeCard = selectedRadio.closest('.et-radio-card');
        if (activeCard) {
            activeCard.classList.add('active');
        }
    }

    // Delete Modal fallback handler (works with or without Bootstrap modal plugin)
    function openDeleteModal() {
        if (window.jQuery && typeof $('#deleteAccountModal').modal === 'function') {
            $('#deleteAccountModal').modal('show');
        } else {
            const modal = document.getElementById('deleteAccountModal');
            if (modal) {
                modal.classList.add('show');
                modal.style.display = 'block';
                document.body.classList.add('modal-open');
            }
        }
    }

    function closeDeleteModal() {
        if (window.jQuery && typeof $('#deleteAccountModal').modal === 'function') {
            $('#deleteAccountModal').modal('hide');
        } else {
            const modal = document.getElementById('deleteAccountModal');
            if (modal) {
                modal.classList.remove('show');
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        }
    }
</script>
@endsection

