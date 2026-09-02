<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New User Registration</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #1e293b; }
        .details { background: #f8fafc; padding: 15px; border-radius: 6px; margin: 20px 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 6px; }
        .footer { margin-top: 20px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔔 New User Registration</h2>
        <p>A new user has registered and is waiting for your approval.</p>
        <div class="details">
            <strong>Name:</strong> {{ $user->name }}<br>
            <strong>Email:</strong> {{ $user->email }}<br>
            <strong>Requested Role:</strong> {{ $user->requestedRole?->name ?? 'N/A' }}
        </div>
        <a href="{{ url('/users') }}" class="btn">Review Users</a>
        <div class="footer">Cash Management System</div>
    </div>
</body>
</html>