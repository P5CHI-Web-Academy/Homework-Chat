<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<!--[if lte IE 9]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade
    your browser</a> to improve your experience and security.</p>
<![endif]-->

<div class="login-page">
    <div class="login-page__form">
        <form class="login-page__form-register" action="/register" method="POST">
            <input type="text" placeholder="Email address" name="email"/>
            <input type="text" placeholder="Name" name="name"/>
            <input type="password" placeholder="Password" name="password"/>
            <button>Create</button>
            <p class="login-page__form-message">Already registered? <a href="#">Sign In</a></p>
        </form>
        <form class="login-page__form-login" action="/login" method="POST">
            <input type="text" placeholder="Email" name="email"/>
            <input type="password" placeholder="Password" name="password"/>
            <button>Login</button>
            <p class="login-page__form-message">Not registered? <a href="#">Create an account</a></p>
        </form>
        {% if hasError %}
            <div class="login-page__error">
                {{ error }}
            </div>
        {% endif %}
    </div>
</div>

<script src="vendor.js"></script>
<script src="scripts.js"></script>
</body>
</html>
