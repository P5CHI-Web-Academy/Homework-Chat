<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
* @Route(name="user.")
*/
class UserController extends AbstractController {
    /**
     * @Route("/", name="login")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function login(Request $request, EntityManagerInterface $em): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('notice', 'Your token is ' . $user->getToken() . '. Please, keep it.');

            return $this->redirectToRoute(
                'chat.show',
                ['token' => $user->getToken(),]
            );
        }

        return $this->render(
            'user/login.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("user/{token}/edit", name="edit", requirements={"token" = "\w+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function edit(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute(
                'user.edit',
                ['token' => $user->getToken(),]
            );
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
