<?php

namespace Framework\Controllers;


use Framework\Core\Controller;
use Framework\Core\Doctrine;
use Framework\Models\User;

defined('BASEPATH') OR exit('No direct script access allowed');

class SessionsController extends Controller
{

    public function showLogin()
    {
        require_once BASEPATH . 'templates/login.php';
    }

    public function login()
    {
        $em = Doctrine::getEntityManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $_POST['email']]);

        if ($user) {
            $_SESSION['loggedIn'] = $user->getId();

            header("Location: /");
        }

        $message = 'Invalid email address';

        require_once BASEPATH . 'templates/login.php';
    }

    public function logout()
    {
        $_SESSION['loggedIn'] = null;

        header("Location: /login");
    }

    public function showRegister()
    {
        require_once BASEPATH . 'templates/register.php';
    }

    public function register()
    {
        $name = $_POST['name'];
        $email = $_POST['email'];

        $em = Doctrine::getEntityManager();
        $user = new User();
        $user->setEmail($email);
        $user->setAvatar('/img/avatar_circle_blue_512dp.png');
        $user->setName($name);

        $em->persist($user);
        $em->flush();

        $_SESSION['loggedIn'] = $user->getId();

        header("Location: /");
    }
}