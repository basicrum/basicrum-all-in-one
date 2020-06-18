<?php

declare(strict_types=1);

namespace App\BasicRum\Visit\Data;

use App\Entity\RumDataFlat;
use App\Entity\VisitsOverview;

class Fetch
{
    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var Filter */
    private $filter;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry = $registry;
        $this->filter = new Filter($registry);
    }

    /**
     * @return mixed
     */
    public function fetchRumDataFlatInRange(int $startId, int $endId): array
    {
        $repository = $this->registry
            ->getRepository(RumDataFlat::class);

        $query = $repository->createQueryBuilder('rdf')
            ->where("rdf.rumDataId >= '".$startId."' AND rdf.rumDataId <= '".$endId."'")
            ->andWhere('rdf.deviceTypeId != :deviceTypeId')
            ->setParameter('deviceTypeId', $this->filter->getBotDeviceTypeId())
            ->select(['rdf.rtSi', 'rdf.createdAt', 'rdf.rumDataId', 'rdf.urlId'])
            ->orderBy('rdf.rumDataId', 'DESC')
            ->getQuery();

        return $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @return mixed
     */
    public function fetchRumDataFlatInRangeForSession(int $startId, int $endId, string $rtSi): array
    {
        $repository = $this->registry
            ->getRepository(RumDataFlat::class);

        $query = $repository->createQueryBuilder('rdf')
            ->where("rdf.pageViewId >= '".$startId."' AND rdf.rumDataId <= '".$endId."'")
            ->andWhere('rdf.deviceTypeId != :deviceTypeId')
            ->andWhere('rdf.rtSi = :rtSi')
            ->setParameter('deviceTypeId', $this->filter->getBotDeviceTypeId())
            ->setParameter('rtSi', $rtSi)
            ->select(['rdf.rtSi', 'rdf.createdAt', 'rdf.rumDataId', 'rdf.urlId'])
            ->getQuery();

        return $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }

    public function fetchNotCompletedVisits(): array
    {
        $repository = $this->registry
            ->getRepository(VisitsOverview::class);

        $query = $repository->createQueryBuilder('vo')
            ->where('vo.completed = 0')
            ->select(['vo'])
            ->getQuery();

        $visits = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        $transformed = [];

        foreach ($visits as $visit) {
            $transformed[$visit['firstPageViewId']] = $visit;
        }

        return $transformed;
    }

    public function fetchPreviousLastScannedPageViewId(): int
    {
        $repository = $this->registry
            ->getRepository(VisitsOverview::class);

        $rumDataId = (int) $repository->createQueryBuilder('vo')
            ->select('MAX(vo.lastPageViewId)')
            ->getQuery()
            ->getSingleScalarResult();

        return $rumDataId;
    }

    public function fetchPreviousSessionPageView(array $pageView): array
    {
        $repository = $this->registry
            ->getRepository(RumDataFlat::class);

        $rumDataId = $pageView['rumDataId'];
        $rtSi = $pageView['rtSi'];

        $query = $repository->createQueryBuilder('rdf')
            ->where("rdf.rumDataId < '".$rumDataId."'")
            ->andWhere('rdf.rtSi = :rtSi')
            ->setParameter('rtSi', $rtSi)
            ->select(['rdf.createdAt', 'rdf.rumDataId', 'rdf.rtSi'])
            ->orderBy('rdf.rumDataId', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $res = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        return $res[0] ?? [];
    }
}
