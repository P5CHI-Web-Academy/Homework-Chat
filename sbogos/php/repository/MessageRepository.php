<?php
namespace Framework\Repository;

use Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository
{
    public function getMessages()
    {
        $qb = $this->createQueryBuilder('m');
        $qb
            ->select('m, u')
            ->join('m.user', 'u')
            ->orderBy('m.createdAt', 'asc')
            ->setMaxResults(4)
        ;

        return $qb->getQuery()->getResult();
    }

    public function findByLoggedInUserAndChatUser($loggedUser, $chatUser)
    {
        return $this->createQueryBuilder('m')
            ->where('m.chatGroup = :group')
            ->orWhere('m.chatGroup = :rgroup')
            ->setParameter('group', $loggedUser . ':' . $chatUser)
            ->setParameter('rgroup', $chatUser . ':' . $loggedUser)
            ->getQuery()
            ->getResult();
    }
}