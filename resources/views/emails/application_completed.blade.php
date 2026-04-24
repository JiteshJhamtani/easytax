<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f7fa; margin: 0; padding: 40px 20px; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header { background-color: #1e9c5d; padding: 35px 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.5px; }
        .body { padding: 40px 30px; color: #334155; line-height: 1.6; font-size: 16px; }
        .greeting { font-size: 18px; font-weight: 600; color: #0f172a; margin-bottom: 20px; }
        .status-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 20px; margin: 25px 0; }
        .status-row { margin-bottom: 10px; font-size: 15px; }
        .status-row:last-child { margin-bottom: 0; }
        .label { color: #166534; font-weight: 700; display: inline-block; width: 130px; }
        .value { color: #0f172a; font-weight: 600; }
        .cta-container { text-align: center; margin: 35px 0 20px; }
        .btn { display: inline-block; background-color: #1e9c5d; color: #ffffff !important; text-decoration: none; padding: 14px 28px; border-radius: 6px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px rgba(30, 156, 93, 0.25); transition: background-color 0.2s; }
        .btn:hover { background-color: #166534; }
        .footer { background-color: #f8fafc; padding: 25px 30px; text-align: center; color: #64748b; font-size: 13px; border-top: 1px solid #e2e8f0; }
        .link-text { word-break: break-all; font-size: 12px; color: #94a3b8; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Application Completed!</h1>
        </div>
        
        <div class="body">
            <div class="greeting">Namaste, {{ $clientName ?? 'Valued Client' }}!</div>
            
            <p>Great news! Your application for <strong>{{ $application->service->name ?? 'our service' }}</strong> has been successfully processed and marked as Completed.</p>
            
            <div class="status-box">
                <div class="status-row"><span class="label">Application ID:</span> <span class="value">#{{ $application->id }}</span></div>
                <div class="status-row"><span class="label">Completed On:</span> <span class="value">{{ now()->format('d M Y, h:i A') }}</span></div>
                <div class="status-row"><span class="label">Handling Agent:</span> <span class="value">{{ $application->agent->name ?? 'EasyTax Team' }}</span></div>
            </div>

            <p>If there are any final documents (like a GST Certificate, Receipt, or Acknowledgement), your agent will be handing them over to you shortly.</p>
            
            <div class="cta-container">
                <a href="{{ $trackingUrl }}" class="btn">View Status Details</a>
            </div>

            <p class="link-text">If the button doesn't work, copy and paste this link into your browser:<br> {{ $trackingUrl }}</p>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} EasyTax. This is an automated message, please do not reply directly to this email.
        </div>
    </div>
</body>
</html>