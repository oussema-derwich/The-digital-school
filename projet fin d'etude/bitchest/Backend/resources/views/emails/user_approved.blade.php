<!DOCTYPE html>
<html>
<head>
    <title>Account Approved</title>
    <style>
        .logo {
            max-width: 150px;
            height: auto;
        }
        .email-container {
            font-family: Arial, sans-serif;
            color: #333;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            max-width: 600px;
            margin: 0 auto;
        }
        .email-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .email-body {
            line-height: 1.6;
        }
        .email-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="email-container">

        <div class="email-header">
            <h1>Bitchest</h1>
            <h1>Your Account Has Been Approved</h1>
        </div>

        <!-- Email body -->
        <div class="email-body">
            <p>Hello,</p>
            <p>Here are your credentials to access the application:</p>
            <ul>
                <li><strong>Email:</strong> {{ $email }}</li>
                <li><strong>Password:</strong> {{ $password }}</li>
            </ul>
            <p>Click the link below to access the application:</p>
            <p><a href="{{ $loginUrl }}">Connect to Bitchest</a></p>
            <p>Thank you for your trust!</p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>This email was sent automatically. Please do not reply.</p>
        </div>
    </div>
</body>
</html>
