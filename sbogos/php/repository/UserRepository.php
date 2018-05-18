<?php
namespace Framework\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findAllExceptMe($user)
    {
        return $this->createQueryBuilder('u')
            ->where('u.id != :id')
            ->setParameter('id', $user)
            ->getQuery()
            ->getResult();
    }
}