<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>
 **/

namespace App\Repository;

use App\Entity\Fiainana;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Fiainana|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fiainana|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fiainana[]    findAll()
 * @method Fiainana[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FiainanaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fiainana::class);
    }

    /**
     * @param string $search
     *
     * @return mixed
     */
    public function findByAjax(string $search)
    {
        return $this->createQueryBuilder('u')
            ->where('u.description LIKE :s')
            ->setParameter('s','%'.$search.'%')
            ->getQuery()->getResult();
    }
}