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

    {{-- B2B Partner Certificate Generator Card --}}
    <div class="et-card mb-4" id="b2b-certificate">
        <div class="et-card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="et-card-icon bg-warning-soft text-warning">
                    <i class="fas fa-certificate fs-5"></i>
                </div>
                <div>
                    <h3 class="et-card-title d-flex align-items-center gap-2 mb-1">
                        B2B Partner Certificate Generator
                        <span class="custom-badge badge-success-soft text-success text-xs font-weight-bold px-2 py-0.5 rounded-pill">
                            <i class="fas fa-check-circle mr-1"></i> Accredited Partner
                        </span>
                    </h3>
                    <p class="et-card-subtitle mb-0">
                        Customize your accreditation details with Indian honorific titles, preview in real-time, and download your official certificate.
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge badge-light border text-muted px-3 py-2 text-xs font-weight-bold">
                    <i class="fas fa-award text-success mr-1"></i> EasyTax B2B Partner
                </span>
            </div>
        </div>

        <div class="et-card-body p-3 p-md-4">
            <div class="row align-items-center">
                {{-- Left: Customization Form --}}
                <div class="col-lg-5 col-xl-5 mb-4 mb-lg-0">
                    <div class="cert-controls-panel p-3 p-md-4 rounded-xl">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <span class="font-weight-bold text-dark small text-uppercase tracking-wider">
                                <i class="fas fa-sliders-h text-success mr-1"></i> Customization Options
                            </span>
                            <span class="badge badge-pill badge-light border text-muted text-xs">Dynamic Preview</span>
                        </div>

                        {{-- Honorific Prefix --}}
                        <div class="form-group mb-3">
                            <label for="certPrefix" class="form-label font-weight-bold text-dark small mb-1 d-flex align-items-center justify-content-between">
                                <span><i class="fas fa-heading text-primary mr-1"></i> Honorific Prefix (Front Title)</span>
                                <span class="text-muted text-xs font-weight-normal">Optional</span>
                            </label>
                            <select id="certPrefix" class="form-control et-select-custom" onchange="updateCertificate()">
                                <option value="">[None] No Prefix</option>
                                <option value="Shree">Shree (श्री)</option>
                                <option value="Shrimati">Shrimati (श्रीमती)</option>
                                <option value="Mr.">Mr.</option>
                                <option value="Mrs.">Mrs.</option>
                                <option value="Ms.">Ms.</option>
                            </select>
                        </div>

                        {{-- Honorific Suffix --}}
                        <div class="form-group mb-3">
                            <label for="certSuffix" class="form-label font-weight-bold text-dark small mb-1 d-flex align-items-center justify-content-between">
                                <span><i class="fas fa-tag text-info mr-1"></i> Honorific Suffix (End Respect Word)</span>
                                <span class="badge badge-success-soft text-success text-xs font-weight-bold">Respectful</span>
                            </label>
                            <select id="certSuffix" class="form-control et-select-custom" onchange="updateCertificate()">
                                <option value="">[None] No Suffix</option>
                                <option value="Ji" selected>Ji (जी) - Respectful (Default)</option>
                                <option value="Sahab">Sahab (साहब)</option>
                            </select>
                        </div>

                        {{-- Quick Honorific Presets --}}
                        <div class="mb-3">
                            <div class="text-xs text-muted font-weight-bold mb-1">Quick Honorific Presets:</div>
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1" onclick="applyPreset('', 'Ji')">
                                    Name + Ji
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1" onclick="applyPreset('Shree', 'Ji')">
                                    Shree + Name + Ji
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1" onclick="applyPreset('Shree', 'Sahab')">
                                    Shree + Name + Sahab
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1" onclick="applyPreset('Shree', '')">
                                    Shree Only
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1" onclick="applyPreset('', '')">
                                    Plain Name
                                </button>
                            </div>
                        </div>

                        {{-- Live Formatted Display Badge --}}
                        <div class="alert alert-light border rounded-lg p-2.5 mb-3 bg-white shadow-2xs">
                            <div class="text-xs text-muted font-weight-bold text-uppercase mb-1 d-flex align-items-center justify-content-between">
                                <span><i class="fas fa-eye text-primary mr-1"></i> Name on Certificate:</span>
                                <span class="badge badge-success-soft text-success text-2xs">Live</span>
                            </div>
                            <div id="formattedNamePreview" class="h5 font-weight-bold mb-0" style="font-family: 'Great Vibes', 'Alex Brush', 'Brush Script MT', cursive; font-size: 1.85rem; color: #0c2340; line-height: 1.2;">
                                {{ $user->name }} Ji
                            </div>
                            <div class="text-2xs text-muted mt-1.5 pt-1 border-top d-flex align-items-center justify-content-between">
                                <span><i class="fas fa-user-check text-success mr-1"></i> Profile Name: <strong id="partnerNameDisplay" class="text-dark">{{ $user->name }}</strong></span>
                                <span class="badge badge-light border text-muted"><i class="fas fa-lock mr-1"></i> From Profile</span>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex flex-column gap-2 mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-success font-weight-bold py-2.5 shadow-sm d-flex align-items-center justify-content-center gap-2 rounded-lg" onclick="downloadCertificatePNG()">
                                <i class="fas fa-download mr-1"></i> Download Certificate (PNG Image)
                            </button>
                            
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-dark font-weight-bold py-2 flex-grow-1 shadow-sm d-flex align-items-center justify-content-center gap-2 rounded-lg" onclick="downloadCertificatePDF()">
                                    <i class="fas fa-file-pdf text-danger mr-1"></i> Download PDF
                                </button>
                                <button type="button" class="btn btn-outline-secondary font-weight-bold py-2 px-3 shadow-sm rounded-lg" onclick="printCertificate()" title="Print Certificate">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary font-weight-bold py-2 px-3 shadow-sm rounded-lg" onclick="resetCertificateForm()" title="Reset to Defaults">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Right: Live Interactive Canvas Preview --}}
                <div class="col-lg-7 col-xl-7">
                    <div class="cert-preview-wrapper p-3 p-md-4 rounded-xl text-center">
                        <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-pill badge-primary-soft text-primary font-weight-bold px-2.5 py-1">
                                    <i class="fas fa-desktop mr-1"></i> Live Certificate Canvas
                                </span>
                                <span id="fontStatusPill" class="badge badge-pill badge-success-soft text-success px-2.5 py-1 text-xs font-weight-bold">
                                    <i class="fas fa-check-circle mr-1"></i> Live Preview Ready
                                </span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-light text-white py-1 px-2.5 rounded-lg text-xs" onclick="expandCertModal()">
                                <i class="fas fa-expand-alt mr-1"></i> View Fullscreen
                            </button>
                        </div>

                        @php
                            $certTemplateFile = public_path('assets/images/b2b_partner_certificate_template.jpg');
                            $certTemplateData = file_exists($certTemplateFile) ? base64_encode(file_get_contents($certTemplateFile)) : '';
                            $certFallbackUrl = asset('assets/images/b2b_partner_certificate_template.jpg');
                        @endphp
                        {{-- Embedded Offline/Instant Template Asset --}}
                        <img id="certTemplateImg" 
                             src="{{ !empty($certTemplateData) ? 'data:image/jpeg;base64,' . $certTemplateData : $certFallbackUrl }}" 
                             data-template-src="{{ $certFallbackUrl }}"
                             style="display:none;" 
                             onload="if(typeof updateCertificate === 'function') updateCertificate();" 
                             alt="EasyTax Certificate Template">

                        {{-- Hidden font preloader elements to force browser to load Google Fonts immediately --}}
                        <div style="position: absolute; left: -9999px; top: -9999px; visibility: hidden; pointer-events: none;" aria-hidden="true">
                            <span style="font-family: 'Great Vibes', cursive;">Preload Great Vibes Calligraphy</span>
                            <span style="font-family: 'Dancing Script', cursive;">Preload Dancing Script Signature</span>
                            <span style="font-family: 'Montserrat', sans-serif;">Preload Montserrat Date</span>
                        </div>

                        <div class="cert-canvas-container position-relative rounded-lg overflow-hidden shadow-lg">
                            <canvas id="certCanvas" width="1024" height="724" class="w-100 h-auto d-block"></canvas>
                        </div>

                        <div class="d-flex flex-wrap align-items-center justify-content-between mt-3 text-white-50 text-xs px-1">
                            <span><i class="fas fa-shield-alt text-success mr-1"></i> Authorized Signatory: Rahul Sharma</span>
                            <span><i class="fas fa-calendar-check text-info mr-1"></i> Issue Date: {{ now()->format('jS M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fullscreen Certificate Modal --}}
    <div class="modal fade" id="certFullscreenModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content rounded-xl overflow-hidden border-0 shadow-2xl">
                <div class="modal-header bg-dark text-white py-2 px-3 align-items-center">
                    <h5 class="modal-title h6 mb-0 d-flex align-items-center gap-2">
                        <i class="fas fa-certificate text-warning"></i> EasyTax B2B Partner Certificate Preview
                    </h5>
                    <button type="button" class="close text-white" onclick="closeCertModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-2 bg-light text-center">
                    <img id="certModalImage" src="" alt="Certificate Full Preview" class="img-fluid rounded shadow-sm">
                </div>
                <div class="modal-footer bg-white py-2 px-3 justify-content-between">
                    <span class="text-muted small">Accredited EasyTax Partner Document</span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-success font-weight-bold" onclick="downloadCertificatePNG()">
                            <i class="fas fa-download mr-1"></i> Download PNG
                        </button>
                        <button type="button" class="btn btn-sm btn-dark font-weight-bold" onclick="downloadCertificatePDF()">
                            <i class="fas fa-file-pdf mr-1"></i> Download PDF
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="closeCertModal()">
                            Close
                        </button>
                    </div>
                </div>
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
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Caveat:wght@600;700&family=Dancing+Script:wght@600;700&family=Great+Vibes&family=Montserrat:wght@500;600;700&display=swap" rel="stylesheet">
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

    /* ── B2B CERTIFICATE GENERATOR STYLES ── */
    .cert-controls-panel {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }

    .cert-preview-wrapper {
        background: linear-gradient(145deg, #0a192f 0%, #172a45 100%);
        border: 1px solid #203a61;
        border-radius: 16px;
        box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.25);
    }

    .cert-canvas-container {
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.5);
        background: #ffffff;
        border: 2px solid rgba(255, 255, 255, 0.12);
        transition: transform 0.2s ease;
    }

    .et-select-custom {
        height: calc(1.5em + 0.9rem + 2px);
        font-weight: 500;
        border-radius: 8px;
        border-color: #CBD5E1;
        font-size: 0.9rem;
    }

    .et-select-custom:focus {
        border-color: var(--et-green);
        box-shadow: 0 0 0 3px rgba(30, 156, 93, 0.15);
    }

    .badge-primary-soft {
        background-color: #EBF5FF;
        color: #1E40AF;
    }

    .btn-xs {
        padding: 0.2rem 0.55rem;
        font-size: 0.75rem;
        line-height: 1.2;
    }

    .gap-1 { gap: 0.25rem !important; }
    .gap-2 { gap: 0.5rem !important; }
    .gap-3 { gap: 1rem !important; }
    .gap-4 { gap: 1.5rem !important; }
    .text-2xs { font-size: 0.7rem !important; }
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

    // ── B2B PARTNER CERTIFICATE GENERATOR LOGIC ──
    const defaultAgentName = @json($user->name);
    const certTemplateFallbackUrl = @json($certFallbackUrl);
    const certBase64 = @if(!empty($certTemplateData)) "data:image/jpeg;base64,{{ $certTemplateData }}" @else certTemplateFallbackUrl @endif;

    // Dedicated Image object for canvas background
    const certBgImage = new Image();
    certBgImage.onload = function() {
        updateCertificate();
    };
    certBgImage.onerror = function() {
        if (certBgImage.src !== certTemplateFallbackUrl) {
            certBgImage.src = certTemplateFallbackUrl;
        }
    };
    certBgImage.src = certBase64;

    function getAgentName() {
        const profileInput = document.getElementById('name');
        if (profileInput && profileInput.value && profileInput.value.trim()) {
            return profileInput.value.trim();
        }
        return defaultAgentName || 'Partner';
    }

    function getFormattedName() {
        const prefix = (document.getElementById('certPrefix')?.value || '').trim();
        const name = getAgentName();
        const suffix = (document.getElementById('certSuffix')?.value || '').trim();

        let parts = [];
        if (prefix) parts.push(prefix);
        if (name) parts.push(name);
        if (suffix) parts.push(suffix);

        return parts.join(' ');
    }

    function applyPreset(prefix, suffix) {
        const prefixSelect = document.getElementById('certPrefix');
        const suffixSelect = document.getElementById('certSuffix');
        if (prefixSelect) prefixSelect.value = prefix;
        if (suffixSelect) suffixSelect.value = suffix;
        updateCertificate();
    }

    function resetCertificateForm() {
        const prefixSelect = document.getElementById('certPrefix');
        const suffixSelect = document.getElementById('certSuffix');

        if (prefixSelect) prefixSelect.value = '';
        if (suffixSelect) suffixSelect.value = 'Ji';

        updateCertificate();
    }

    // Sync dynamically when user types in the Personal Information "Full Name" input
    const profileNameInput = document.getElementById('name');
    if (profileNameInput) {
        profileNameInput.addEventListener('input', updateCertificate);
    }

    function updateCertificate() {
        const canvas = document.getElementById('certCanvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');

        const currentAgentName = getAgentName();
        const partnerNameDisplay = document.getElementById('partnerNameDisplay');
        if (partnerNameDisplay) {
            partnerNameDisplay.textContent = currentAgentName;
        }

        const formattedName = getFormattedName();
        const previewEl = document.getElementById('formattedNamePreview');
        if (previewEl) {
            previewEl.textContent = formattedName || '(No Name)';
        }

        const certDate = "{{ now()->format('jS M Y') }}";

        // 1. Draw template background
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        let drewImage = false;
        if (certBgImage.complete && certBgImage.naturalWidth > 0) {
            try {
                ctx.drawImage(certBgImage, 0, 0, canvas.width, canvas.height);
                drewImage = true;
            } catch (e) {
                console.warn('Canvas drawImage error:', e);
            }
        }
        
        if (!drewImage) {
            const domImg = document.getElementById('certTemplateImg');
            if (domImg && domImg.complete && domImg.naturalWidth > 0) {
                try {
                    ctx.drawImage(domImg, 0, 0, canvas.width, canvas.height);
                    drewImage = true;
                } catch (e) {}
            }
        }

        // 2. Draw Recipient Full Name
        // Centered at x = 512, line is at y = 420, baseline at y = 412
        ctx.save();
        ctx.textAlign = 'center';
        ctx.textBaseline = 'alphabetic';
        ctx.fillStyle = '#0c2340';

        let fontSize = 48;
        ctx.font = `italic ${fontSize}px 'Great Vibes', 'Alex Brush', 'Dancing Script', 'Brush Script MT', cursive`;

        // Automatically downscale if name exceeds line width
        const maxTextWidth = 620;
        let textWidth = ctx.measureText(formattedName).width;
        while (textWidth > maxTextWidth && fontSize > 22) {
            fontSize -= 2;
            ctx.font = `italic ${fontSize}px 'Great Vibes', 'Alex Brush', 'Dancing Script', 'Brush Script MT', cursive`;
            textWidth = ctx.measureText(formattedName).width;
        }

        ctx.fillText(formattedName, 512, 412);
        ctx.restore();

        // 3. Draw Issue Date
        // Centered over "DATE" at x = 278, line at y = 625, baseline at y = 614
        ctx.save();
        ctx.textAlign = 'center';
        ctx.textBaseline = 'alphabetic';
        ctx.fillStyle = '#0c2340';
        ctx.font = `600 18px 'Montserrat', 'Plus Jakarta Sans', sans-serif`;
        ctx.fillText(certDate, 278, 614);
        ctx.restore();

        // 4. Draw Signatory "Rahul Sharma"
        // Centered over "SIGNATURE" at x = 760, line at y = 625, baseline at y = 612
        // Blue ballpoint / fountain pen ink color
        ctx.save();
        ctx.textAlign = 'center';
        ctx.textBaseline = 'alphabetic';
        ctx.fillStyle = '#1d4ed8'; // Blue signature ink
        ctx.font = `italic 600 32px 'Dancing Script', 'Caveat', 'Brush Script MT', cursive`;
        ctx.fillText('Rahul Sharma', 760, 612);
        ctx.restore();
    }

    // Font ready listener without throwing
    try {
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(function() {
                updateCertificate();
            }).catch(function() {});
        }
    } catch (e) {}

    // Immediate & Scheduled Renders
    updateCertificate();
    document.addEventListener('DOMContentLoaded', updateCertificate);
    window.addEventListener('load', updateCertificate);
    setTimeout(updateCertificate, 50);
    setTimeout(updateCertificate, 200);
    setTimeout(updateCertificate, 500);
    setTimeout(updateCertificate, 1000);

    // Download PNG (1024x724 lossless image)
    function downloadCertificatePNG() {
        const canvas = document.getElementById('certCanvas');
        if (!canvas) return;

        updateCertificate();

        const formattedName = getFormattedName().replace(/[^a-zA-Z0-9_\- ]/g, '').trim() || 'Partner';
        const filename = `EasyTax_B2B_Certificate_${formattedName.replace(/\s+/g, '_')}.png`;

        const link = document.createElement('a');
        link.download = filename;
        link.href = canvas.toDataURL('image/png');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Download PDF (A4 Landscape)
    function downloadCertificatePDF() {
        const canvas = document.getElementById('certCanvas');
        if (!canvas) return;

        updateCertificate();

        const formattedName = getFormattedName().replace(/[^a-zA-Z0-9_\- ]/g, '').trim() || 'Partner';
        const filename = `EasyTax_B2B_Certificate_${formattedName.replace(/\s+/g, '_')}.pdf`;

        if (window.jspdf && window.jspdf.jsPDF) {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({
                orientation: 'landscape',
                unit: 'mm',
                format: 'a4'
            });
            const imgData = canvas.toDataURL('image/jpeg', 0.98);
            pdf.addImage(imgData, 'JPEG', 0, 0, 297, 210);
            pdf.save(filename);
        } else {
            printCertificate();
        }
    }

    // Print Certificate (opens native print with zero margins)
    function printCertificate() {
        const canvas = document.getElementById('certCanvas');
        if (!canvas) return;

        updateCertificate();

        const printWin = window.open('', '_blank');
        if (!printWin) {
            alert('Please allow popups to print your certificate.');
            return;
        }

        const dataUrl = canvas.toDataURL('image/png');
        printWin.document.title = 'EasyTax B2B Partner Certificate';
        printWin.document.body.style.margin = '0';
        printWin.document.body.style.display = 'flex';
        printWin.document.body.style.alignItems = 'center';
        printWin.document.body.style.justifyContent = 'center';
        printWin.document.body.style.backgroundColor = '#ffffff';

        const printImg = printWin.document.createElement('img');
        printImg.src = dataUrl;
        printImg.style.width = '100vw';
        printImg.style.height = '100vh';
        printImg.style.objectFit = 'contain';

        printWin.document.body.appendChild(printImg);

        printImg.onload = function() {
            setTimeout(function() {
                printWin.focus();
                printWin.print();
                printWin.close();
            }, 300);
        };
    }

    // Fullscreen Modal Preview
    function expandCertModal() {
        const canvas = document.getElementById('certCanvas');
        if (!canvas) return;
        updateCertificate();
        const dataUrl = canvas.toDataURL('image/png');
        const modalImg = document.getElementById('certModalImage');
        if (modalImg) {
            modalImg.src = dataUrl;
        }
        if (window.jQuery && typeof $('#certFullscreenModal').modal === 'function') {
            $('#certFullscreenModal').modal('show');
        } else {
            const modal = document.getElementById('certFullscreenModal');
            if (modal) {
                modal.classList.add('show');
                modal.style.display = 'block';
                document.body.classList.add('modal-open');
            }
        }
    }

    function closeCertModal() {
        if (window.jQuery && typeof $('#certFullscreenModal').modal === 'function') {
            $('#certFullscreenModal').modal('hide');
        } else {
            const modal = document.getElementById('certFullscreenModal');
            if (modal) {
                modal.classList.remove('show');
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        }
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
@endsection

