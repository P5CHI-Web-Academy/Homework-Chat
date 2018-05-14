<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param User $user
     * @param User $interlocutor
     * @return Message[]
     */
    public function findDialogueMessagesByUsers(User $user, User $interlocutor): array
    {
        return $this->createQueryBuilder('m')
            ->orWhere('m.sender = :user AND m.addressee = :interlocutor')
            ->orWhere('m.sender = :interlocutor AND m.addressee = :user')
            ->setParameters([
                'user' => $user,
                'interlocutor' => $interlocutor,
            ])
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Even if this method is not used at the moment, it will be useful in the future
     * @param int $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findMessageById(int $id)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param User $addressee
     * @param User $sender
     * @return Message[]
     */
    public function findUnreadMessagesByUsers(User $addressee, User $sender): array
    {
        return $this->createQueryBuilder('m')
            ->select('m')
            ->andWhere('m.sender = :sender')
            ->andWhere('m.addressee = :addressee')
            ->andWhere('m.seen = :seen')
            ->setParameters([
                'sender' => $sender,
                'addressee' => $addressee,
                'seen' => false,
            ])
            ->getQuery()
            ->getResult();
    }
}
