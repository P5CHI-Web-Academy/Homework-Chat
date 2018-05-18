<?php
namespace Framework\Core;

use Framework\Controllers\DefaultController;
use Framework\Controllers\MessagesController;
use Framework\Controllers\SessionsController;

defined('BASEPATH') OR exit('No direct script access allowed');

session_start();

if (!defined('NO_CONTROLLER_RUN')) {
    $method = $_SERVER['REQUEST_METHOD'] ?? null;

    $uri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
    if ($uri == '/') {
        runController(DefaultController::class, 'index');
    }

    else if ($uri == '/users' and $method == 'GET') {
        runController(DefaultController::class, 'users');
    }

    else if ($uri == '/user' and $method == 'GET') {
        runController(DefaultController::class, 'user');
    }

    else if ($uri == '/avatar' and $method == 'POST') {
        runController(DefaultController::class, 'avatar');
    }

    else if ($uri == '/store' and $method == 'POST') {
        runController(MessagesController::class, 'store');
    }

    else if ($uri == '/messages' and $method == 'GET') {
        runController(MessagesController::class, 'messages');
    }

    else if ($uri == '/login' and $method == 'GET') {
        runController(SessionsController::class, 'showLogin');
    }

    else if ($uri == '/login' and $method == 'POST') {
        runController(SessionsController::class, 'login');
    }

    else if ($uri == '/logout' and $method == 'POST') {
        runController(SessionsController::class, 'logout');
    }

    else if ($uri == '/register' and $method == 'GET') {
        runController(SessionsController::class, 'showRegister');
    }

    else if ($uri == '/register' and $method == 'POST') {
        runController(SessionsController::class, 'register');
    }

    else {
        show404();
    }
}