<!DOCTYPE html>
<html>
<head>
    <title>Welcome Admin</title>
    <style>
        .button {
            background-color: #3490dc;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Hello {{ $user->name }},</h2>
    <p>You have been registered as an <strong>Admin</strong> in the system.</p>

    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Password:</strong> {{ $plainPassword }}</p>

    <a href="{{ env('APP_URL') }}" class="button">Login to your Dashboard</a>

    <p>Thank you,<br/>The Admin Team</p>
</body>
</html>
