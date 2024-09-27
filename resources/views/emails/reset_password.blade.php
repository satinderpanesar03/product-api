<!-- resources/views/emails/reset_password.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        .otp {
            font-size: 24px;
            font-weight: bold;
            color: #007BFF;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Reset Your Password</h1>
    <p>Hi {{ $user->name }},</p>
    <p>We received a request to reset your password. Use the following OTP to proceed:</p>
    <div class="otp">{{ $otp }}</div>
    <p>This OTP is valid for 2 minutes.</p>
    <p>If you did not request a password reset, please ignore this email.</p>
    <div class="footer">
        <p>Thank you,<br>Laravel</p>
    </div>
</div>
</body>
</html>
