<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        form {
            display: flex;
            justify-content: center;
            flex-direction: column;
        }

        form * {
            margin: 5px 0;
        }

        input {
            height: 38px;
            width: 400px;
        }

        button {
            align-self: flex-start;
        }
    </style>
</head>
<body>

<?php echo $message ?? ''; ?>

<form action="/login" method="post">
    <label for="email">Email Address</label>
    <input type="email" name="email" id="email">
    <button type="submit">Login</button> or <a href="/register">register</a>
</form>



</body>
</html>