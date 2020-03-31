<?php

namespace App\Repository;

use App\Entity\Widgets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Widgets|null find($id, $lockMode = null, $lockVersion = null)
 * @method Widgets|null findOneBy(array $criteria, array $orderBy = null)
 * @method Widgets[]    findAll()
 * @method Widgets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WidgetsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Widgets::class);
    }

    // /**
    //  * @return Widgets[] Returns an array of Widgets objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Widgets
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
