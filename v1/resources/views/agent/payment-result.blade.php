@extends('layouts.agent')

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
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const transactionId = "{{ request()->query('txn') }}";
        let pollCount = 0;

        function updateUI(status) {
            const icon = document.getElementById('statusIcon');
            const title = document.getElementById('statusTitle');
            const subtitle = document.getElementById('statusSubtitle');
            const action = document.getElementById('actionArea');
            const progress = document.querySelector('.progress-container');

            // Hide progress bar once finished
            progress.style.opacity = "0";
            icon.className = "status-icon transition-in";
            icon.innerHTML = "";

            if (status === "SUCCESS") {
                icon.classList.add("success");
                icon.innerHTML =
                    '<svg viewBox="0 0 52 52" class="checkmark"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>';
                title.innerText = "Payment Successful";
                title.style.color = "#18b36b";
                subtitle.innerText = "Everything looks good! Your application has been submitted.";
            } else {
                icon.classList.add("failed");
                icon.innerHTML = '✕';
                title.innerText = "Payment Declined";
                title.style.color = "#dc3545";
                subtitle.innerText = "We couldn't process this transaction. Please check your bank details and try again.";
            }

            action.style.display = "block";
            action.classList.add("fade-up");
        }

        function pollStatus() {
            pollCount++;
            // Visual feedback that polling is happening
            document.getElementById('progressBar').style.width = `${Math.min(pollCount * 10, 95)}%`;

            fetch(`/payment/status/${transactionId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === "SUCCESS" || data.status === "FAILED") {
                        document.getElementById('progressBar').style.width = "100%";
                        setTimeout(() => updateUI(data.status), 500);
                        clearInterval(polling);
                    }
                })
                .catch(() => {});
        }

        let polling = setInterval(pollStatus, 3000);
    </script>
@endsection

@push('styles')
    <style>
        :root {
            --success-color: #18b36b;
            --error-color: #dc3545;
            --bg-subtle: #f8fafc;
        }

        .payment-wrapper {
            min-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
        }

        .payment-card {
            background: #ffffff;
            width: 100%;
            max-width: 480px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }

        /* Polling Progress Bar */
        .progress-container {
            height: 4px;
            width: 100%;
            background: #eee;
            position: absolute;
            top: 0;
            transition: opacity 0.3s;
        }

        .progress-bar {
            height: 100%;
            width: 5%;
            background: var(--brand-primary);
            transition: width 0.4s ease;
        }

        .card-body {
            padding: 50px 40px;
            text-align: center;
        }

        /* Animated Spinner */
        .status-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .status-icon.processing {
            background: #f0f4f8;
        }

        .status-icon.success {
            background: rgba(24, 179, 107, 0.1);
            color: var(--success-color);
        }

        .status-icon.failed {
            background: rgba(220, 53, 69, 0.1);
            color: var(--error-color);
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(0, 0, 0, 0.05);
            border-top-color: var(--brand-primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        /* Meta Info Styling */
        .payment-meta {
            background: var(--bg-subtle);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .meta-row:last-child {
            margin-bottom: 0;
        }

        .meta-row .label {
            color: #718096;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .meta-row .value {
            color: #2d3748;
            font-weight: 600;
            font-size: 15px;
        }

        .meta-row .amount {
            color: var(--brand-primary);
            font-size: 18px;
        }

        /* Button Styling */
        .primary-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            background: var(--brand-primary);
            color: #fff;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
            transition: transform 0.2s;
        }

        .primary-btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        /* Animations */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .fade-up {
            animation: fadeUp 0.5s ease forwards;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Success Checkmark Animation */
        .checkmark__circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 2;
            stroke-miterlimit: 10;
            stroke: var(--success-color);
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }

        .checkmark {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: block;
            stroke-width: 2;
            stroke: var(--success-color);
            stroke-miterlimit: 10;
            margin: 10% auto;
            box-shadow: inset 0px 0px 0px var(--success-color);
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        }

        .checkmark__check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }

        @keyframes stroke {
            100% {
                stroke-dashoffset: 0;
            }
        }

        @keyframes scale {

            0%,
            100% {
                transform: none;
            }

            50% {
                transform: scale3d(1.1, 1.1, 1);
            }
        }
    </style>
@endpush
