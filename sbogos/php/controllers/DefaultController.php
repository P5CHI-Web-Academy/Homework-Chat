<?php

namespace Framework\Controllers;

use Framework\Core\Controller;
use Framework\Core\Doctrine;
use Framework\Models\User;

defined('BASEPATH') OR exit('No direct script access allowed');

class DefaultController extends Controller
{
    public function index()
    {
        if (!$_SESSION['loggedIn']) {
            return header("Location: /login");
        }

        $em = Doctrine::getEntityManager();

        $user = $em->getRepository(User::class)->findOneBy(['id' => $_SESSION['loggedIn']]);

        require_once BASEPATH . 'templates/index.php';
    }

    public function users()
    {
        $em = Doctrine::getEntityManager();

        $users = $em->getRepository(User::class)->findAllExceptMe($_SESSION['loggedIn']);

        $jsonData = array_map(function (User $user) {
            $std = new \stdClass();
            $std->id = $user->getId();
            $std->email = $user->getEmail();
            $std->name = $user->getName();
            $std->avatar = $user->getAvatar();

            return $std;
        }, $users);

        echo json_encode($jsonData);
    }

    public function user()
    {
        $em = Doctrine::getEntityManager();

        $user = $em->getRepository(User::class)->findOneBy(['id' => $_SESSION['loggedIn']]);

        $std = new \stdClass();
        $std->id = $user->getId();
        $std->email = $user->getEmail();
        $std->name = $user->getName();
        $std->avatar = $user->getAvatar();

        echo json_encode($std);
    }

    public function avatar()
    {
        $path = BASEPATH . 'public/img/avatars/';
        $file = time() . basename($_FILES['avatar']['name']);

        $name = '/img/avatars/' . $file;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $path . $file)) {

            $em = Doctrine::getEntityManager();
            $user = $em->getRepository(User::class)->findOneBy(['id' => $_POST['user']]);
            $user->setAvatar($name);
            $em->persist($user);
            $em->flush();

        }
    }
}