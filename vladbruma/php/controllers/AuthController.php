<?php
namespace Framework\Controllers;

use Doctrine\ORM\EntityManager;
use Framework\Core\Controller;
use Framework\Core\Doctrine;
use Framework\Models\Message;
use Framework\Models\User;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends Controller
{
    /**
     * @var \Respect\Validation\Validator
     */
    private $userValidator;

    /**
     * Initialize validator
     */
    protected function init()
    {
        parent::init();

        $this->userValidator =
            v::attribute('email', v::stringType()
                ->length(1, 50)
                ->email())
            ->attribute('name', v::stringType()
                ->length(1, 50))
            ->attribute('password', v::stringType()
                    ->length(1, 255));
    }

    /**
     * Display login/register page
     *
     * @throws \Exception
     */
    public function indexAction()
    {
        $this->renderTemplate('login.php');
    }

    /**
     * Login user
     *
     * @throws \Exception
     */
    public function login()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        /** @var EntityManager $em */
        $em = Doctrine::getEntityManager();
        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $email
        ]);

        if (!$user || !password_verify($password, $user->getPassword())) {
            $this->renderTemplate('login.php', [
                'hasError' => true,
                'error' => 'Username or password is invalid'
            ]);

            die();
        }

        $_SESSION['user'] = $user->getId();

        header('Location: /');
        die();
    }

    /**
     * Register new user
     *
     * @throws \Exception
     */
    public function register()
    {
        $user = new User();
        $user->populate();

        try {
            $this->userValidator->assert($user);

            /** @var EntityManager $em */
            $em = Doctrine::getEntityManager();

            $em->persist($user);
            $em->flush();

            $_SESSION['user'] = $user->getId();

            header('Location: /');
            die();
        } catch(NestedValidationException $exception) {
            $this->renderTemplate('login.php', [
                'hasError' => true,
                'error' => nl2br($exception->getFullMessage())
            ]);
        }
    }

    /**
     * Logout and redirect to login screen
     */
    public function logout()
    {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }

        header('Location: /login');
        die();
    }
}
