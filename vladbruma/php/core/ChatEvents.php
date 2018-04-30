<?php

namespace Framework\Core;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatEvents implements MessageComponentInterface
{
    protected $clients;

    protected $connectedUsers = [];

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";

        $response = [
            'type'  => 'refreshUsers',
            'users' => array_values($this->connectedUsers)
        ];

        $conn->send(json_encode($response));
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        $response = [];
        switch ($data['type']) {
            case 'online':
                $this->connectedUsers[$from->resourceId] = $data['id'];
                $response = $data;

                foreach ($this->clients as $client) {
                    if ($from !== $client) {
                        $client->send(json_encode($response));
                    }
                }
                break;
            case 'message':
                $receiver = $data['receiver'];
                $clientId = array_search($receiver, $this->connectedUsers);
                $response = $data;

                foreach ($this->clients as $client) {
                    if ($client->resourceId === $clientId) {
                        $client->send(json_encode($response));
                    }
                }

                break;
            case 'typing':
                $response = $data;

                foreach ($this->clients as $client) {
                    if ($from !== $client) {
                        $client->send(json_encode($response));
                    }
                }
                break;
        }

        echo "New message: {$msg}\n";
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        if (isset($this->connectedUsers[$conn->resourceId])) {
            unset($this->connectedUsers[$conn->resourceId]);
        }

        $response = [
            'type'  => 'refreshUsers',
            'users' => array_values($this->connectedUsers)
        ];

        foreach ($this->clients as $client) {
            $client->send(json_encode($response));
        }

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
