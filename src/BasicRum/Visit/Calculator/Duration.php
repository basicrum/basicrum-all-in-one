<?php

declare(strict_types=1);

namespace App\BasicRum\Visit\Calculator;

use App\Entity\NavigationTimings;
use App\Entity\VisitsOverview;

class Duration
{

    /** @var  \Symfony\Bridge\Doctrine\RegistryInterface */
    private $registry;

    public function __construct(\Symfony\Bridge\Doctrine\RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param NavigationTimings $firstPageView
     * @param NavigationTimings $lastPageView
     * @return int
     */
    public function calculatePageViewsDurationDuration(NavigationTimings $firstPageView, NavigationTimings $lastPageView)
    {
        return $lastPageView->getCreatedAt()->getTimestamp() - $firstPageView->getCreatedAt()->getTimestamp();
    }

    /**
     * @param array $visit
     * @return int
     */
    public function calculateAfterLastVisitDuration(array $visit)
    {
        $repository = $this->registry
            ->getRepository(VisitsOverview::class);

        $query = $repository->createQueryBuilder('vo')
            ->where("vo.firstPageViewId < :firstPageViewId")
            ->andWhere(("vo.guid = (:guid)"))
            ->setParameter('firstPageViewId', $visit['firstPageViewId'])
            ->setParameter('guid', $visit['guid'])
            ->select(['vo.lastPageViewId'])
            ->orderBy('vo.lastPageViewId', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $res = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_SCALAR);

        if (empty($res)) {
            return 0;
        }

        return $this->calculatePageViewsDurationDuration($res[0]['lastPageViewId'], $visit['firstPageViewId']);
    }

}