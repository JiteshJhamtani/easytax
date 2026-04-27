<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f7fa; margin: 0; padding: 20px; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header { background-color: #ffffff; padding: 30px; text-align: center; border-bottom: 3px solid #1e9c5d; }
        .header img { max-width: 180px; height: auto; }
        .body { padding: 40px 30px; color: #334155; line-height: 1.6; font-size: 16px; }
        .greeting { font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 20px; }
        .success-banner { background-color: #1e9c5d; color: #ffffff; padding: 15px; text-align: center; border-radius: 8px; font-weight: 600; font-size: 18px; margin-bottom: 25px; }
        .status-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 25px 0; }
        .status-row { margin-bottom: 10px; font-size: 15px; }
        .status-row:last-child { margin-bottom: 0; }
        .label { color: #64748b; font-weight: 600; display: inline-block; width: 130px; }
        .value { color: #0f172a; font-weight: 700; }
        .cta-container { text-align: center; margin: 30px 0; }
        .btn { display: inline-block; background-color: #1e9c5d; color: #ffffff !important; text-decoration: none; padding: 14px 28px; border-radius: 6px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px rgba(30, 156, 93, 0.25); }
        
        /* Promo Section */
        .promo-section { background-color: #f0fdf4; padding: 30px; border-top: 1px solid #bbf7d0; }
        .promo-title { font-size: 18px; font-weight: 700; color: #166534; text-align: center; margin-bottom: 20px; margin-top: 0; }
        .service-list { list-style: none; padding: 0; margin: 0; }
        .service-item { background: #ffffff; padding: 15px; border-radius: 8px; margin-bottom: 10px; border: 1px solid #dcfce3; display: flex; align-items: center; }
        .service-icon { font-size: 24px; margin-right: 15px; }
        .service-text h4 { margin: 0 0 5px 0; color: #0f172a; font-size: 15px; }
        .service-text p { margin: 0; color: #64748b; font-size: 13px; line-height: 1.4; }
        .promo-btn { display: block; text-align: center; color: #1e9c5d; font-weight: 600; text-decoration: none; margin-top: 20px; font-size: 15px; }
        
        .footer { background-color: #0f172a; padding: 30px; text-align: center; color: #94a3b8; font-size: 13px; }
        .footer a { color: #1e9c5d; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
<img src="{{ asset('assets/images/logo11.png') }}" alt="EasyTax Logo">
        </div>
        
        <div class="body">
            <div class="success-banner">🎉 Application Successfully Completed!</div>
            
            <div class="greeting">Namaste, {{ $clientName ?? 'Valued Client' }}!</div>
            
            <p>Great news! Your application for <strong>{{ $application->service->name ?? 'our service' }}</strong> has been fully processed and verified by our team.</p>
            
            <div class="status-box">
                <div class="status-row"><span class="label">App ID:</span> <span class="value">#{{ $application->id }}</span></div>
                <div class="status-row"><span class="label">Completed On:</span> <span class="value">{{ now()->format('d M Y, h:i A') }}</span></div>
                <div class="status-row"><span class="label">Your Agent:</span> <span class="value">{{ $application->agent->name ?? 'EasyTax Team' }}</span></div>
            </div>
            
            <div class="cta-container">
                <a href="{{ $trackingUrl }}" class="btn">View Final Status & Documents</a>
            </div>
        </div>

        <div class="promo-section">
            <h3 class="promo-title">What's Next for Your Business?</h3>
            <ul class="service-list">
                <li class="service-item">
                    <div class="service-icon">📊</div>
                    <div class="service-text">
                        <h4>GST Registration & Filing</h4>
                        <p>Stay compliant and avoid penalties. Let EasyTax handle your monthly and quarterly GST returns effortlessly.</p>
                    </div>
                </li>
                
                <li class="service-item">
                    <div class="service-icon">🏢</div>
                    <div class="service-text">
                        <h4>Pvt Ltd / Section 8 Registration</h4>
                        <p>Scale your business with proper incorporation. Get limited liability protection and attract investors.</p>
                    </div>
                </li>

                <li class="service-item">
                    <div class="service-icon">💼</div>
                    <div class="service-text">
                        <h4>ITR Filing (Individual/Business)</h4>
                        <p>Maximize your refunds and keep your financial records spotless with our expert CA-assisted filing.</p>
                    </div>
                </li>
            </ul>
            
            <a href="{{ route('services.index') }}" class="promo-btn">Explore All EasyTax Services &rarr;</a>
        </div>
        
        <div class="footer">
            <p>Need help? Contact your agent directly or call us at <strong><a href="tel:+917725981022">+91 77259 81022</a></strong></p>
            <p>&copy; {{ date('Y') }} EasyTax. All rights reserved.</p>
        </div>
    </div>
</body>
</html>