<?php

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use \App\Entity\User;

class UserTokenListener
{
    /**
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            $entity->setToken(\bin2hex(\random_bytes(10)));
        }
    }
}
