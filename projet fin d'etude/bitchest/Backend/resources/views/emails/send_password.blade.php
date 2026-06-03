<!-- resources/views/emails/send_password.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your temporary password</title>
</head>
<body>
    <h1>Hi {{ $name }},</h1>
    <p>Your temporary password is : <strong>{{ $password }}</strong></p>
    <p>Thank you for signing in to BitChest!</p>
</body>
</html>

