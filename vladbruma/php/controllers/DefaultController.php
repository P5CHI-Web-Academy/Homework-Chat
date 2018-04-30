<?php
namespace Framework\Controllers;

use Doctrine\ORM\EntityManager;
use Framework\Core\ChatEvents;
use Framework\Core\Controller;
use Framework\Core\Doctrine;
use Framework\Models\Message;
use Framework\Models\User;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

defined('BASEPATH') OR exit('No direct script access allowed');

class DefaultController extends Controller
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * Pre-checks and initialization
     */
    protected function init()
    {
        parent::init();

        // Redirect if unauthenticated
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            die();
        }

        $this->em = Doctrine::getEntityManager();
        $this->user = $this->em->getRepository(User::class)->find($_SESSION['user']);
    }

    /**
     * Main chat page
     *
     * @throws \Exception
     */
    public function indexAction()
    {
        $allUsers = $this->em->getRepository(User::class)->findAll();
        $allUsersArr = [];

        $unseenMessages = $this->em->getRepository(Message::class)->getUnseenMessages($this->user);

        /** @var User $user */
        foreach ($allUsers as $user) {
            if ($user->getId() === $this->user->getId()) {
                continue;
            }

            $contact = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail()
            ];

            $contact['unseen'] = isset($unseenMessages[$user->getId()]) ?
                $unseenMessages[$user->getId()]['unseenTotal'] : 0;

            $allUsersArr[] = $contact;
        }

        $this->renderTemplate('index.php', [
            'user' => [
                'id' => $this->user->getId(),
                'name' => $this->user->getName(),
                'email' => $this->user->getEmail()
            ],
            'allUsers' => $allUsersArr
        ]);
    }

    /**
     * Fetch conversation between users
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function getMessages()
    {
        header('Content-Type: application/json');

        $userValidator = v::key('receiver', v::notEmpty()->setName('Receiver'));
        if (!$userValidator->validate($_GET)) {
            http_response_code(400);
            echo json_encode(['error' => 'Cannot retrieve messages']);

            die();
        }

        $currentUser = $this->user;
        $receiverId = intval($_GET['receiver']);
        $receiver = $this->em->getRepository(User::class)->find($receiverId);

        $messages = $this->em->getRepository(Message::class)->getConversation($currentUser, $receiver);

        $jsonData = array_map(function (Message $msg) use ($currentUser) {
            $std = new \stdClass();
            $std->id = $msg->getId();
            $std->message = $msg->getMessage();
            $std->time = $msg->getCreatedAt()->format('H:i');
            $std->type = $msg->getSender()->getId() == $currentUser->getId() ? 'user' : 'receiver';

            return $std;
        }, $messages);

        echo json_encode(array_reverse($jsonData));
    }

    /**
     * Save a new message
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postMessage()
    {
        header('Content-Type: application/json');

        $messageValidator = v::key('message', v::notEmpty()->stringType()->prnt()->length(1, 500)->setName('Message'));
        $userValidator    = v::key('receiver', v::notEmpty()->setName('Receiver'));

        if (!$messageValidator->validate($_POST) || !$userValidator->validate($_POST)) {
            http_response_code(400);
            echo json_encode(['error' => 'Message could not be saved']);

            die();
        }

        $messageText = $_POST['message'] ?? null;
        $receiverId = intval($_POST['receiver']);
        $receiver = $this->em->getRepository(User::class)->find($receiverId);
        $sender = $this->user;

        $message = new Message();
        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setMessage($messageText);

        $this->em->persist($message);
        $this->em->flush();

        http_response_code(200);
        echo json_encode([
            'time' => $message->getCreatedAt()->format('H:i'),
            'sender' => $sender->getId(),
            'receiver' => $receiver->getId(),
            'message' => $message->getMessage()
        ]);
    }

    /**
     * Set messages as seen by the current user
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function setSeen()
    {
        header('Content-Type: application/json');

        $userValidator = v::key('sender', v::notEmpty()->setName('Sender'));
        if (!$userValidator->validate($_POST)) {
            http_response_code(400);
            echo json_encode(['error' => 'Message could not be saved']);

            die();
        }

        $senderId = intval($_POST['sender']);
        $this->em->getRepository(Message::class)->setSeen($senderId, $this->user->getId());

        http_response_code(200);
        echo json_encode(['success' => true]);
    }
}
