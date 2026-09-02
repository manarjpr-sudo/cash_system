<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Operation</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #d97706; }
        .details { background: #f8fafc; padding: 15px; border-radius: 6px; margin: 20px 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 6px; }
        .footer { margin-top: 20px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <h2>📋 New Operation Pending Approval</h2>
        <p>Created by: {{ $creator->name }}</p>
        <div class="details">
            <strong>ID:</strong> #{{ $operation->id }}<br>
            <strong>Type:</strong> {{ $operation->type }}<br>
            <strong>Amount:</strong> {{ number_format($operation->amount, 2) }}<br>
            <strong>Description:</strong> {{ $operation->description ?? 'N/A' }}
        </div>
        <a href="{{ url('/operations') }}" class="btn">Review Operations</a>
        <div class="footer">Cash Management System</div>
    </div>
</body>
</html>