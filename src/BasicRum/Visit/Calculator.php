<?php

declare(strict_types=1);

namespace App\BasicRum\Visit;

use App\Entity\NavigationTimings;
use App\Entity\VisitsOverview;

use App\BasicRum\Visit\Calculator\Filter;
use App\BasicRum\Visit\Calculator\Aggregator;

class Calculator
{

    /** @var int */
    private $scannedChunkSize     = 1000;

    /** @var int */
    private $sessionExpireMinutes = 30;

    /** @var \Symfony\Bridge\Doctrine\RegistryInterface */
    private $registry;

    /** @var Filter */
    private $filter;

    /** @var Aggregator */
    private $aggregator;

    public function __construct(\Symfony\Bridge\Doctrine\RegistryInterface $registry)
    {
        $this->registry   = $registry;
        $this->filter     = new Filter($registry);
        $this->aggregator = new Aggregator($this->sessionExpireMinutes);
    }

    public function calculate()
    {
        $lastPageViewId = $this->_getPreviousLastScannedPageViewId();

        $navTimingsRes = $this->_getNavTimingsInRange($lastPageViewId + 1, $lastPageViewId + $this->scannedChunkSize);

        $notCompletedVisits = $this->_getNotCompletedVisits();

        foreach ($notCompletedVisits as $notCompletedVisit)
        {
            $views = $this->_getNavTimingsInRangeForSession(
                $notCompletedVisit['firstPageViewId'],
                $notCompletedVisit['lastPageViewId'],
                $notCompletedVisit['guid']
            );

            foreach ($views as $view) {
                $this->aggregator->addPageView($view);
            }
        }

        foreach ($navTimingsRes as $nav)
        {
            $this->aggregator->addPageView($nav);
        }

        $visits = $this->aggregator->generateVisits($notCompletedVisits);

        return $visits;
    }

    /**
     * @param int $startId
     * @param int $endId
     * @return mixed
     */
    private function _getNavTimingsInRange(int $startId, int $endId)
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
    private function _getNavTimingsInRangeForSession(int $startId, int $endId, string $guid)
    {
        $repository = $this->registry
            ->getRepository(NavigationTimings::class);

        $query = $repository->createQueryBuilder('nt')
            ->where("nt.pageViewId >= '" . $startId . "' AND nt.pageViewId <= '" . $endId . "'")
            ->andWhere('nt.deviceTypeId != :deviceTypeId')
            ->setParameter('deviceTypeId', $this->filter->getBotDeviceTypeId())
            ->select(['nt.guid', 'nt.createdAt', 'nt.pageViewId', 'nt.urlId'])
            ->getQuery();

        return $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @return array
     */
    private function _getNotCompletedVisits()
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
     * @return bool|int
     */
    private function _getPreviousLastScannedPageViewId()
    {
        $repository = $this->registry
            ->getRepository(VisitsOverview::class);

        $pageViewId = (int) $repository->createQueryBuilder('vo')
            ->select('MAX(vo.lastPageViewId)')
            ->getQuery()
            ->getSingleScalarResult();

        return $pageViewId;
    }

}