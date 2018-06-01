<?php

namespace Framework\Repository;

use Doctrine\ORM\EntityRepository;
use Framework\Models\User;

class MessageRepository extends EntityRepository
{
    /**
     * Fetch messages between specified users
     *
     * @param User $firstUser
     * @param User $secondUser
     * @param int $limit
     * @return mixed
     */
    public function getConversation(User $firstUser, User $secondUser, $limit = 10)
    {
        $qb = $this->createQueryBuilder('m');
        $qb
            ->where($qb->expr()->andX(
                $qb->expr()->eq('m.sender', $firstUser->getId()),
                $qb->expr()->eq('m.receiver', $secondUser->getId())
            ))
            ->orWhere($qb->expr()->andX(
                $qb->expr()->eq('m.sender', $secondUser->getId()),
                $qb->expr()->eq('m.receiver', $firstUser->getId())
            ))
            ->orderBy('m.createdAt', 'desc')
            ->setMaxResults($limit)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * Get all unseen messages for current user
     *
     * @param User $user
     * @return array
     */
    public function getUnseenMessages(User $user): array
    {
        $qb = $this->createQueryBuilder('m');
        $qb
            ->select(['count(m.id) AS unseenTotal', 's.id'])
            ->join('m.sender', 's', null, null, 's.id')
            ->where('m.receiver = :userId')
            ->andWhere('m.wasSeen = :wasSeen')
            ->setParameters([
                'userId' => $user->getId(),
                'wasSeen' => false
            ])
            ->groupBy('m.sender')
        ;

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Update messages as seen between users
     *
     * @param int $senderId
     * @param int $receiverId
     */
    public function setSeen(int $senderId, int $receiverId): void
    {
        $this->createQueryBuilder('m')
            ->update()
            ->set( 'm.wasSeen', 1 )
            ->where('m.sender = :senderId')
            ->andWhere('m.receiver = :receiverId')
            ->setParameters([
                'senderId' => $senderId,
                'receiverId' => $receiverId
            ])
            ->getQuery()->execute();
    }
}
