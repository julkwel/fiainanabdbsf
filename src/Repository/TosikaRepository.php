<?php

namespace App\Repository;

use App\Entity\Tosika;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Tosika|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tosika|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tosika[]    findAll()
 * @method Tosika[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TosikaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tosika::class);
    }

    // /**
    //  * @return Tosika[] Returns an array of Tosika objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tosika
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
