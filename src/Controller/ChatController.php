<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

/**
 * @Route(name="chat.")
 */
class ChatController extends AbstractController {

    /**
     * @Route("user/{token}", name="show", requirements={"token" = "\w+"})
     * @Method("GET")
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findUsers();

        return $this->render('chat/show.html.twig', [
            'me' => $user,
            'users' => $users,
        ]);
    }

    /**
     * @Route("user/{token}/{interlocutorId}", name="view", requirements={"token" = "\w+", "interlocutorId" = "\d+"})
     * @Method("GET")
     * @param User $user
     * @param int $interlocutorId
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function view(User $user, int $interlocutorId): Response
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findUsers();

        $interlocutorUser = $this->getDoctrine()
            ->getRepository(User::class)
            ->findUserById($interlocutorId);

        if ($interlocutorUser) {
            return $this->render('chat/view.html.twig', [
                'me' => $user,
                'interlocutorUser' => $interlocutorUser,
                'users' => $users,
            ]);
        }

        return $this->redirectToRoute(
            'chat.show', [
                'token' => $user->getToken(),
            ]
        );
    }

    /**
     * @Route("messages/{token}/{interlocutorId}", name="getMessages", requirements={"token" = "\w+", "interlocutorId" = "\d+"})
     * @Method("GET")
     * @param User $user
     * @param int $interlocutorId
     * @param EntityManagerInterface $em
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMessages(User $user, int $interlocutorId, EntityManagerInterface $em): Response
    {
        $interlocutorUser = $this->getDoctrine()
            ->getRepository(User::class)
            ->findUserById($interlocutorId);

        if ($interlocutorUser) {
            $messages = $this->getDoctrine()
                ->getRepository(Message::class)
                ->findDialogueMessagesByUsers($user, $interlocutorUser);

            $result = [];
            foreach ($messages as $message) {
                if ($message->getAddressee() === $user) {
                    $message->setSeen(true);
                    $em->flush();
                }

                $result[] = [
                    'id' => $message->getId(),
                    'userId' => $message->getSender()->getId(),
                    'createdAt' => $message->getCreatedAt()->format('d/m/Y H:i'),
                    'message' => $message->getMessageText(),
                ];
            }

            return new JsonResponse($result);
        }

        return new JsonResponse(['error' => 'Such user doesn\'t exit']);
    }

    /**
     * @Route("unreadMessagesCount/{token}", name="getUnreadMessagesCount", requirements={"token" = "\w+"})
     * @Method("GET")
     * @param User $user
     * @return JsonResponse
     */
    public function getUnreadMessagesCount(User $user): JsonResponse
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findUsers();

        $result = [];

        foreach ($users as $sender) {
            $unreadMessages = $this->getDoctrine()
                ->getRepository(Message::class)
                ->findUnreadMessagesByUsers($user, $sender);

            $result[] = [
                'userId' => $sender->getId(),
                'msgCount' => \count($unreadMessages),
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("user/{token}/send", name="send", requirements={"token" = "\w+"})
     * @Method("POST")
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function send(User $user, Request $request, EntityManagerInterface $em): ?JsonResponse
    {
        $messageText = $request->request->get('message');

        $validMessage = v::notEmpty()
            ->stringType()
            ->prnt()
            ->length(1, 1024)
            ->setName('Message');
        try {
            $validMessage->assert($messageText);

            $interlocutorId = $request->request->get('interlocutorId');
            $interlocutorUser = $this->getDoctrine()
                ->getRepository(User::class)
                ->findUserById($interlocutorId);

            $message = new Message();
            $message->setSeen(false);
            $message->setMessageText($messageText);
            $message->setSender($user);
            $message->setAddressee($interlocutorUser);

            $em->persist($message);
            $em->flush();

            $result = [
                'id' => $message->getId(),
                'userId' => $message->getSender()->getId(),
                'createdAt' => $message->getCreatedAt()->format('d/m/Y H:i'),
                'message' => $message->getMessageText(),
                'success' => 'Message has been sent',
            ];
            return new JsonResponse($result);
        }
        catch (NestedValidationException $e) {
            return new JsonResponse(['error' => $e->getFullMessage()]);
        }
    }
}
