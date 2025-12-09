<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication QR Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .qr-container {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
        }
        .secret-container {
            margin: 20px 0;
            padding: 15px;
            background-color: #fff;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .secret-code {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            color: #2c3e50;
            letter-spacing: 2px;
        }
        .instructions {
            margin-top: 20px;
            padding: 15px;
            background-color: #e8f4f8;
            border-left: 4px solid #3498db;
            border-radius: 4px;
        }
        .instructions h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        .instructions ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        .warning {
            margin-top: 20px;
            padding: 15px;
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 4px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Two-Factor Authentication Setup</h2>
        
        <p>Hello {{ $userName }},</p>
        
        <p>Please find your Two-Factor Authentication (2FA) QR code attached to this email as a PDF document. The PDF contains your QR code and setup instructions.</p>
        
        <div class="qr-container" style="background-color: #e8f4f8; border: 2px solid #3498db;">
            <h3 style="margin-top: 0; color: #2c3e50;">📎 PDF Attachment</h3>
            <p style="font-size: 16px; margin: 10px 0;">
                <strong>mfa-qr-code.pdf</strong> has been attached to this email.
            </p>
            <p style="font-size: 14px; color: #666;">
                Open the PDF to view your QR code and follow the setup instructions.
            </p>
        </div>
        
        <div class="secret-container">
            <p style="margin-top: 0;"><strong>Or manually enter this secret code:</strong></p>
            <div class="secret-code">{{ $secret }}</div>
        </div>
        
        <div class="instructions">
            <h3>How to set up 2FA:</h3>
            <ol>
                <li>Download an authenticator app (Google Authenticator, Microsoft Authenticator, or Authy) on your mobile device.</li>
                <li>Open the attached PDF file (<strong>mfa-qr-code.pdf</strong>) to view your QR code.</li>
                <li>Open the authenticator app and select "Add account" or the "+" button.</li>
                <li>Choose "Scan QR code" and scan the QR code from the PDF, or manually enter the secret code shown below.</li>
                <li>Enter the 6-digit code from your authenticator app on the setup page to complete the process.</li>
            </ol>
        </div>
        
        <div class="warning">
            <strong>⚠️ Security Notice:</strong> Keep the PDF attachment and secret code confidential. Do not share them with anyone. After completing your 2FA setup, please delete the PDF attachment for security reasons. If you did not request this email, please contact support immediately.
        </div>
        
        <p style="margin-top: 30px;">
            Best regards,<br>
            {{ config('app.name') }} Team
        </p>
    </div>
</body>
</html>

