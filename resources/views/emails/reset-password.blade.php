<!DOCTYPE html>
<html>
<head>
    <title>Reset Your Password</title>
    <style>
        .button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <p>You are receiving this email because we received a password reset request for your account.</p>

    <p>Your password reset token is:</p>

    <a href="{{ url('reset-password?token=' . $token) }}" class="button">Change Passsword</a>

    <p>Please click the button above to reset your password. If the button doesn't work, you can copy and paste the token into the password reset page of our application.</p>

    <p>If you did not request a password reset, no further action is required.</p>
</body>
</html>
