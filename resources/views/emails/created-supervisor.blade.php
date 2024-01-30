<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
        }
        img {
            width: 100px;
        }
    </style>
</head>

<body>
    <img src="{{ URL::asset('images/logo.png')  }}" alt="">
    <p>Dear {{ $user->first_name . ' ' . $user->last_name }},</p>

    <p>Welcome to LNU Internship! Below are your login credentials:</p>
    <p>Username: <code>{{ $user->username }}</code></p>
    <p>Password: <code>{{ $password }}</code></p>

    <p>Please use these credentials to log in to your account. We recommend changing your password upon your first login for security reasons.</p>

    <a href="{{ config('app.fontend_url') . '/supervisor-login/' }}">Login</a>

    <p>
        From: <strong> T & AM System </strong> <br>
        <i>Please refrain from forwarding this email to help you to keep your account secure.</i>
    </p>
</body>

</html>
