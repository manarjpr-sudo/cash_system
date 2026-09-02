<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Approved</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #059669; }
        .details { background: #f8fafc; padding: 15px; border-radius: 6px; margin: 20px 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #059669; color: #fff; text-decoration: none; border-radius: 6px; }
        .footer { margin-top: 20px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <h2>✅ Account Approved!</h2>
        <p>Dear {{ $user->name }},</p>
        <p>Your account has been approved and activated successfully.</p>
        <div class="details">
            <strong>Role:</strong> {{ $user->role?->name ?? 'N/A' }}<br>
            <strong>Status:</strong> Active
        </div>
        <a href="{{ url('/login') }}" class="btn">Login Now</a>
        <div class="footer">Cash Management System</div>
    </div>
</body>
</html>