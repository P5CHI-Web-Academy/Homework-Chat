<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController {

    /**
     * @Route("/", name="chat.main")
     * @Method("GET")
     * @return Response
     */
    public function mainAction(): Response
    {
        return $this->render(
            'chat/main.html.twig', [
            'a'=> 'test'
        ]);
    }
}
