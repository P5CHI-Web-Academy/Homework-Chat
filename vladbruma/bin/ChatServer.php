<?php

define('BASEPATH', __DIR__ . '/../');

require_once BASEPATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once BASEPATH . 'php/core/ChatEvents.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Framework\Core\ChatEvents;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatEvents()
        )
    ),
    8081
);

$server->run();
