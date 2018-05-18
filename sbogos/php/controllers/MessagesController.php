<?php

namespace Framework\Controllers;

use Framework\Core\Controller;
use Framework\Models\Message;
use Framework\Models\User;
use Respect\Validation\Validator as v;
use Framework\Core\Doctrine;

defined('BASEPATH') OR exit('No direct script access allowed');

class MessagesController extends Controller
{
    public function messages()
    {
        header('Content-Type: application/json');

        /** @var EntityManager $em */
        $em = Doctrine::getEntityManager();

        $id = $_GET['chatUser'] ?? 1;

        $messages = $em->getRepository(Message::class)->findByLoggedInUserAndChatUser($_SESSION['loggedIn'], $id);

        $jsonData = array_map(function (Message $msg) {
            $std = new \stdClass();
            $std->id = $msg->getId();
            $std->sender = $msg->getSender();
            $std->message = $msg->getMessage();
            $std->createdAt = $msg->getCreatedAt();

            return $std;
        }, $messages);

        echo json_encode($jsonData);
    }

    public function store()
    {
        header('Content-Type: application/json');

        $validator = v::key('message', v::notEmpty()->stringType()->prnt()->length(1, 1024)->setName('Message'));

        try {
            $validator->assert($_POST);

            $em = Doctrine::getEntityManager();

            $message = $_POST['message'] ?? null;
            $sender = $_POST['sender'] ?? null;
            $receiver = $_POST['receiver'] ?? null;

            $mes = new Message();
            $mes->setSender($sender);
            $mes->setChatGroup($sender . ':' . $receiver);
            $mes->setMessage($message);

            $em->persist($mes);
            $em->flush();

            http_response_code(200);
            echo json_encode(['success' => 'Message sent']);
        }
        catch (NestedValidationException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getFullMessage()]);
        }
    }
}