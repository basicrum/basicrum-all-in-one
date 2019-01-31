<?php

declare(strict_types=1);

namespace App\BasicRum;

use Doctrine\ORM\EntityManager;

use App\BasicRum\Date\DayInterval;
use App\BasicRum\NavigationTiming\MetricAggregator;
use App\BasicRum\Report\Filter\FilterAggregator;
use App\Entity\PageTypeConfig;
use App\Entity\NavigationTimings;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class Report
{

    /* @var \Doctrine\Bundle\DoctrineBundle\Registry $em */
    protected $em;

    /** @var \Symfony\Component\Cache\Adapter\FilesystemAdapter */
    protected $cache;

    /** @var \App\BasicRum\Report\Filter\FilterAggregator */
    protected $filterAggregator;

    /**
     * OneLevel Constructor
     *
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $em
     */
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $em)
    {
        $this->em = $em;
        $this->cache = new FilesystemAdapter('cache.app');
        $this->filterAggregator = new FilterAggregator();
    }

    /**
     * @param array $period
     * @param string $perfMetric
     * @param array $filters
     * @return array
     */
    public function query(array $period, string $perfMetric, array $filters)
    {
        $cacheKey = 'query_report_' . md5($period['start'] . $period['end'] . $perfMetric . print_r($filters, true));

        if ($this->cache->hasItem($cacheKey)) {
            return $this->cache->getItem($cacheKey)->get();
        }

        $samples = $this->_getInMetricInPeriod($period['start'], $period['end'], $perfMetric, $filters);

        $cacheItem = $this->cache->getItem($cacheKey);
        $cacheItem->set($samples);

        $this->cache->save($cacheItem);

        return $samples;
    }

    /**
     * @param string $start
     * @param string $end
     * @param string $perfMetric
     * @param array $filters
     *
     * @return array
     */
    private function _getInMetricInPeriod($start, $end, $perfMetric, array $filters)
    {
        /**
         * Crazy way to construct array key
         *
         * 'nt_res_st' -> 'ntResSt'
         *
         */
        $perfMetricCamelized = $this->_transformToEntityProperty($perfMetric);

        $samples = [];

        $repository = $this->em->getRepository(NavigationTimings::class);

        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        $queryBuilder = $repository->createQueryBuilder('nt');

        $maxId = $this->_getHighestIdInInterval($start, $end);
        $minId = $this->_getLowesIdInInterval($start, $end);

        $queryBuilder
            ->select(['nt.' . $perfMetricCamelized])
            ->where("nt.pageViewId >= '" . $minId . "' AND nt.pageViewId <= '" . $maxId . "'");


        foreach ($filters as $key => $data) {
            if (empty($data['search_value'])) {
                continue;
            }

            $this->filterAggregator->getFilter($key)
                ->attachTo($data['search_value'], $data['condition'], $queryBuilder);
        }

        $navigationTimings = $queryBuilder->getQuery()
            ->getArrayResult();


        /** @var NavigationTimings $nav */
        foreach ($navigationTimings as $nav) {
            if ($nav[$perfMetricCamelized] > 150) {
                $samples[] = $nav[$perfMetricCamelized];
            }
        }

        return $samples;
    }

    private function _getHighestIdInInterval(string $start, string $end)
    {
        $repository = $this->em->getRepository(NavigationTimings::class);

        return $repository->createQueryBuilder('nt')
            ->select('MAX(nt.pageViewId)')
            ->where("nt.createdAt BETWEEN '" . $start . "' AND '" . $end . "'")
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function _getLowesIdInInterval(string $start, string $end)
    {
        $repository = $this->em->getRepository(NavigationTimings::class);

        return $repository->createQueryBuilder('nt')
            ->select('MIN(nt.pageViewId)')
            ->where("nt.createdAt BETWEEN '" . $start . "' AND '" . $end . "'")
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param string $var
     * @return string
     */
    private function _transformToEntityProperty(string $var)
    {
        return lcfirst(str_replace(" ", "",ucwords(str_replace("_", " ", $var))));
    }

    /**
     * @return array
     */
    public function getNavigationTimings()
    {
        $metricAggregator = new MetricAggregator();
        return $metricAggregator->getMetrics();
    }

    /**
     * @return array
     */
    public function getPageTypes()
    {
        $repository = $this->em->getRepository(PageTypeConfig::class);

        $repository->findAll();

        return $repository->findAll();
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function getDoctrine()
    {
        return $this->em;
    }

}