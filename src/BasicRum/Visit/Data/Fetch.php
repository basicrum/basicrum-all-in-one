<?php

declare(strict_types=1);

namespace App\BasicRum\Visit\Data;

use App\Entity\NavigationTimings;
use App\Entity\VisitsOverview;

class Fetch
{

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var Filter */
    private $filter;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry   = $registry;
        $this->filter     = new Filter($registry);
    }

    /**
     * @param int $startId
     * @param int $endId
     * @return mixed
     */
    public function fetchNavTimingsInRange(int $startId, int $endId) : array
    {
        $repository = $this->registry
            ->getRepository(NavigationTimings::class);

        $query = $repository->createQueryBuilder('nt')
            ->where("nt.pageViewId >= '" . $startId . "' AND nt.pageViewId <= '" . $endId . "'")
            ->andWhere('nt.deviceTypeId != :deviceTypeId')
            ->setParameter('deviceTypeId', $this->filter->getBotDeviceTypeId())
            ->select(['nt.guid', 'nt.createdAt', 'nt.pageViewId', 'nt.urlId'])
            ->orderBy('nt.pageViewId', 'DESC')
            ->getQuery();

        return $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @param int $startId
     * @param int $endId
     * @param string $guid
     * @return mixed
     */
    public function fetchNavTimingsInRangeForSession(int $startId, int $endId, string $guid) : array
    {
        $repository = $this->registry
            ->getRepository(NavigationTimings::class);

        $query = $repository->createQueryBuilder('nt')
            ->where("nt.pageViewId >= '" . $startId . "' AND nt.pageViewId <= '" . $endId . "'")
            ->andWhere('nt.deviceTypeId != :deviceTypeId')
            ->andWhere('nt.guid = :guid')
            ->setParameter('deviceTypeId', $this->filter->getBotDeviceTypeId())
            ->setParameter('guid', $guid)
            ->select(['nt.guid', 'nt.createdAt', 'nt.pageViewId', 'nt.urlId'])
            ->getQuery();

        return $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @return array
     */
    public function fetchNotCompletedVisits() : array
    {
        $repository = $this->registry
            ->getRepository(VisitsOverview::class);

        $query = $repository->createQueryBuilder('vo')
            ->where("vo.completed = 0")
            ->select(['vo'])
            ->getQuery();

        $visits = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        $transformed = [];

        foreach ($visits as $visit) {
            $transformed[$visit['firstPageViewId']] = $visit;
        }

        return $transformed;
    }

    /**
     * @return int
     */
    public function fetchPreviousLastScannedPageViewId() : int
    {
        $repository = $this->registry
            ->getRepository(VisitsOverview::class);

        $pageViewId = (int) $repository->createQueryBuilder('vo')
            ->select('MAX(vo.lastPageViewId)')
            ->getQuery()
            ->getSingleScalarResult();

        return $pageViewId;
    }

    /**
     * @param array $pageView
     * @return array
     */
    public function fetchPreviousSessionPageView(array $pageView) : array
    {
        $repository = $this->registry
            ->getRepository(NavigationTimings::class);

        $pageViewId = $pageView['pageViewId'];
        $guid       = $pageView['guid'];

        $query = $repository->createQueryBuilder('nt')
            ->where("nt.pageViewId < '" . $pageViewId . "'")
            ->andWhere('nt.guid = :guid')
            ->setParameter('guid', $guid)
            ->select(['nt.createdAt', 'nt.pageViewId', 'nt.guid'])
            ->orderBy('nt.pageViewId', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $res = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        return $res[0] ?? [];
    }

}