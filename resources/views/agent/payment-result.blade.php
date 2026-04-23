@extends('layouts.agent')

@section('title', 'Payment Status | EasyTax')

@section('css')
    <style>
        /* ── PAGE RESET & VARIABLES ── */
        .content-body { 
            padding: 0 !important; 
            background-color: #F8F9FA; 
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 76px); /* Subtract topbar height */
        }
        
        :root {
            --brand-green: #1E9C5D;
            --brand-red: #EF4444;
            --bg-subtle: #f9fafb;
        }

        .payment-wrapper {
            width: 100%;
            padding: 2rem;
            display: flex;
            justify-content: center;
        }

        /* ── PREMIUM CARD ── */
        .payment-card {
            background: #ffffff;
            width: 100%;
            max-width: 480px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
            position: relative;
        }

        /* ── PROGRESS BAR ── */
        .progress-container {
            height: 4px;
            width: 100%;
            background: #f1f5f9;
            position: absolute;
            top: 0;
            left: 0;
            transition: opacity 0.3s;
        }

        .progress-bar {
            height: 100%;
            width: 5%;
            background: var(--brand-green);
            transition: width 0.4s ease;
        }

        .card-body {
            padding: 3rem 2.5rem;
            text-align: center;
        }

        /* ── ANIMATED ICONS ── */
        .status-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .status-icon.processing { background: #f1f5f9; }
        .status-icon.success { background: #EDF7F4; color: var(--brand-green); }
        .status-icon.failed { background: #FEE2E2; color: var(--brand-red); }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(30, 156, 93, 0.1);
            border-top-color: var(--brand-green);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        .status-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .status-subtitle {
            font-size: 0.95rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        /* ── META INFO (RECEIPT) ── */
        .payment-meta {
            background: var(--bg-subtle);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px dashed #d1d5db;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .meta-row:last-child { margin-bottom: 0; }

        .meta-row .label {
            color: #6b7280;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .meta-row .value {
            color: #111827;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .meta-row .amount {
            color: var(--brand-green);
            font-size: 1.25rem;
            font-weight: 800;
        }

        /* ── PRIMARY BUTTON ── */
        .primary-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.85rem;
            border-radius: 10px;
            background: #111827;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .primary-btn:hover {
            background: #000000;
            color: #ffffff;
            text-decoration: none;
            transform: translateY(-2px);
        }

        /* ── ANIMATIONS ── */
        @keyframes spin { to { transform: rotate(360deg); } }
        .fade-up { animation: fadeUp 0.5s ease forwards; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Success Checkmark Animation */
        .checkmark {
            width: 46px; height: 46px;
            border-radius: 50%;
            display: block;
            stroke-width: 3;
            stroke: var(--brand-green);
            stroke-miterlimit: 10;
            box-shadow: inset 0px 0px 0px var(--brand-green);
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        }
        .checkmark__circle {
            stroke-dasharray: 166; stroke-dashoffset: 166;
            stroke-width: 3; stroke-miterlimit: 10;
            stroke: var(--brand-green); fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }
        .checkmark__check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48; stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }
        @keyframes stroke { 100% { stroke-dashoffset: 0; } }
        @keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.1, 1.1, 1); } }
    </style>
@endsection

@section('content')
    <div class="payment-wrapper">
        <div class="payment-card" id="paymentCard">
            <div class="progress-container">
                <div class="progress-bar" id="progressBar"></div>
            </div>

            <div class="card-body">
                <div id="statusIcon" class="status-icon processing">
                    <div class="spinner"></div>
                </div>

                <h2 id="statusTitle" class="status-title">Confirming Payment</h2>
                <p id="statusSubtitle" class="status-subtitle">
                    Please don't refresh. We're verifying your transaction with the bank...
                </p>

                @if (isset($application))
                    <div class="payment-meta">
                        <div class="meta-row">
                            <span class="label">Application Reference</span>
                            <span class="value">#{{ $application->id }}</span>
                        </div>
                        <div class="meta-row">
                            <span class="label">Total Amount</span>
                            <span class="value amount">₹{{ number_format($application->amount, 2) }}</span>
                        </div>
                    </div>
                @endif

                <div class="action-area" id="actionArea" style="display:none;">
                    <a href="{{ route('agent.dashboard') }}" class="primary-btn">
                        <span>Return to Dashboard</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        const transactionId = "{{ request()->query('txn') }}";
        const urlStatus = "{{ request()->query('status') }}";

        let pollCount = 0;
        let polling;

        function updateUI(status) {
            const icon = document.getElementById('statusIcon');
            const title = document.getElementById('statusTitle');
            const subtitle = document.getElementById('statusSubtitle');
            const action = document.getElementById('actionArea');
            const progress = document.querySelector('.progress-container');

            progress.style.opacity = "0";
            icon.className = "status-icon transition-in";
            icon.innerHTML = "";

            if (status === "SUCCESS") {
                icon.classList.add("success");
                icon.innerHTML =
                    '<svg viewBox="0 0 52 52" class="checkmark"><circle cx="26" cy="26" r="25" fill="none" class="checkmark__circle"/><path fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" class="checkmark__check"/></svg>';
                title.innerText = "Payment Successful";
                title.style.color = "#1E9C5D"; // Updated to Brand Green
                subtitle.innerText = "Your application has been submitted successfully.";
            } else {
                icon.classList.add("failed");
                icon.innerHTML = '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
                title.innerText = "Payment Failed";
                title.style.color = "#EF4444"; // Updated to Brand Red
                subtitle.innerText = "Transaction failed or timed out. Please try again.";
            }

            action.style.display = "block";
            action.classList.add("fade-up");
            action.style.opacity = "1";
            action.style.pointerEvents = "auto";
        }

        function finish(status) {
            document.getElementById('progressBar').style.width = "100%";
            setTimeout(() => updateUI(status), 500);
            clearInterval(polling);
        }

        function pollStatus() {
            pollCount++;

            document.getElementById('progressBar').style.width =
                `${Math.min(pollCount * 10, 95)}%`;

            fetch(`/payment/status/${transactionId}`)
                .then(res => res.json())
                .then(data => {
                    const status = (data.status || '').toUpperCase();

                    if (status === "SUCCESS") {
                        finish("SUCCESS");
                    } else if (status === "FAILED") {
                        finish("FAILED");
                    }
                    // ⛔ still PENDING → continue polling
                })
                .catch(err => {
                    console.error("Polling error:", err);
                });

            // 🛑 STOP after ~30 seconds
            if (pollCount > 10) {
                finish("FAILED");
            }
        }

        // 🚨 Safety: no txn
        if (!transactionId) {
            updateUI("FAILED");
        }
        // ⚡ Instant success (from backend redirect)
        else if (urlStatus === 'success') {
            updateUI("SUCCESS");
        }
        // 🔄 Start polling
        else {
            polling = setInterval(pollStatus, 3000);
        }
    </script>
@endsection