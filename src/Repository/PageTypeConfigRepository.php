<?php

namespace App\Repository;

use App\Entity\PageTypeConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PageTypeConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageTypeConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageTypeConfig[]    findAll()
 * @method PageTypeConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageTypeConfigRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PageTypeConfig::class);
    }

//    /**
//     * @return PageTypeConfig[] Returns an array of PageTypeConfig objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PageTypeConfig
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
