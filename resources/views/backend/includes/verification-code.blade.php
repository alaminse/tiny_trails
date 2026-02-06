<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #333333;
            margin: 0;
        }
        .body {
            font-size: 16px;
            line-height: 1.6;
            color: #555555;
        }
        .code-box {
            background-color: #2d3748;
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 5px;
            padding: 15px 25px;
            text-align: center;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999999;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verification Code</h1>
        </div>
        <div class="body">
            <p>Hello {{ $user->first_name }},</p>
            <p>You requested a verification code. Please use the following code to complete your {{ $type }} verification process:</p>

            <div class="code-box">
                {{ $code }}
            </div>

            <p>This code will expire in 15 minutes. If you did not request this code, please ignore this email.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Your App Name. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
