<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Not Found</title>
    <link rel="shortcut icon" href="<?= $professorpay_favicon ?? '' ?>">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #1a202c;
            color: #a0aec0;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .code {
            font-size: 24px;
            font-weight: 500;
            padding-right: 20px;
            border-right: 1px solid #4a5568;
            letter-spacing: 2px;
            color: #e2e8f0;
        }
        .message {
            font-size: 16px;
            font-weight: 400;
            padding-left: 20px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="code">404</div>
        <div class="message">Not Found</div>
    </div>
</body>
</html>