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
    <p>Hi {{ $full_name }},</p>
    <p>We received a request to reset you Account Password. Please click the button below so we can redirect you to
        password reset page.</p>
    <a href="{{ config('app.fontend_url') . '/reset-password/' . $email . '/' . $token }}">Reset Password</a>

    <p>
        From: <strong> T & AM System </strong> <br>

        <i>Please refrain from forwarding this email to help you to keep your account secure.</i>
    </p>
</body>

</html>