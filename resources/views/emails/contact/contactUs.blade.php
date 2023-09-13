<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message de formulaire de contact</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: center;
            text-align: center;
            background-color: #8E6ECA;
            color: #ffffff;
            padding: 10px 0;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .content {
            margin-top: 5px;
            padding: 20px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1 style="color: white">Nouveau message de formulaire de contact</h1>
    </div>
    <div class="content">
        <p><strong>Nom:</strong> {{ $name }}</p>
        <p><strong>Email:</strong> {{ $email }}</p>
        <p><strong>Message:</strong></p>
        <p>{{ $message }}</p>
    </div>
</div>
</body>
</html>