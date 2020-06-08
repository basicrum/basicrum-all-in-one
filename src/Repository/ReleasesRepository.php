<?php

namespace App\Repository;

use App\Entity\Releases;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SiteSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method SiteSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method SiteSettings[]    findAll()
 * @method SiteSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReleasesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Releases::class);
    }

    public function findAllBetweenDates(DateTime $startDate, DateTime $endDate)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Releases p
            WHERE p.date BETWEEN :date1 AND :date2
            ORDER BY p.date DESC'
        )->setParameter('date1', $startDate->format('Y-m-d'))
        ->setParameter('date2', $endDate->format('Y-m-d'))
        ;

        return $query->getResult();
    }
}
