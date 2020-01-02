<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findAdmin()
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :val')
            ->setParameter('val', '%ROLE_ADMIN%')
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findMembers()
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :val')
            ->andWhere('u.isAbone = :isAbone')
            ->setParameter('val', '%ROLE_USER%')
            ->setParameter('isAbone', true)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
