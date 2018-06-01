<?php
namespace Framework\Core;

use Framework\Controllers\DefaultController;
use Framework\Controllers\AuthController;

defined('BASEPATH') OR exit('No direct script access allowed');

if (!defined('NO_CONTROLLER_RUN')) {
    $method = $_SERVER['REQUEST_METHOD'] ?? null;

    $uri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
    if ($uri == '/') {
        runController(DefaultController::class, 'indexAction');
    }
    else if ($uri == '/post' and $method == 'POST') {
        runController(DefaultController::class, 'postMessage');
    } else if ($uri == '/messages' and $method == 'GET') {
        runController(DefaultController::class, 'getMessages');
    } else if ($uri == '/set-seen' and $method == 'POST') {
        runController(DefaultController::class, 'setSeen');
    } else if ($uri == '/login' and $method == 'GET') {
        runController(AuthController::class, 'indexAction');
    } else if ($uri == '/login' and $method == 'POST') {
        runController(AuthController::class, 'login');
    } else if ($uri == '/register' and $method == 'POST') {
        runController(AuthController::class, 'register');
    } else if ($uri == '/logout' and $method == 'GET') {
        runController(AuthController::class, 'logout');
    } else {
        show404();
    }
}
