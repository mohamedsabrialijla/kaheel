<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Account Credentials</title>
</head>
<body>

    <img src="https://seratech.ai/assets/media/svg/brand-logos/logo.png" alt="SeraTech - Logo">


    <title>Welcome to SeraTech - Credentials Your account</title>

    <h2 style="margin-top:20px;">Hello {{ $user->name ?? $user->email }},</h2>

    <p>Your account has been created successfully.</p>

    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Password:</strong> {{ $password }}</p>

    <p>You can now log in to your account and change your password anytime.</p>

    <p>Thank you,<br>SeraTech Team</p>
</body>
</html>
