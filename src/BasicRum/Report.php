<?php

declare(strict_types=1);

namespace App\BasicRum;

use Doctrine\ORM\EntityManager;

use App\BasicRum\Date\DayInterval;
use App\BasicRum\NavigationTiming\MetricAggregator;
use App\BasicRum\Report\Filter\FilterAggregator;
use App\Entity\PageTypeConfig;
use App\Entity\NavigationTimings;
use Symfony\Component\Cache\Simple\FilesystemCache;

class Report
{

    /* @var \Doctrine\Bundle\DoctrineBundle\Registry $em */
    protected $em;

    /** @var \Symfony\Component\Cache\Simple\FilesystemCache */
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
        $this->cache = new FilesystemCache();
        $this->filterAggregator = new FilterAggregator();
    }

    /**
     * @param array $period
     * @param string $perfMetric
     * @return array
     */
    public function query(array $period, string $perfMetric, $filters)
    {
        $cacheKey = 'test-dddaa' . md5($period['start'] . $period['end'] . $perfMetric . print_r($filters, true));

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $samples = $this->_getInMetricInPeriod($period['start'], $period['end'], $perfMetric, $filters);
        $this->cache->set($cacheKey, $samples);

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

        $queryBuilder
            ->select(['nt.' . $perfMetricCamelized])
            ->where("nt.createdAt BETWEEN '" . $start . "' AND '" . $end . "'");


        foreach ($filters as $key => $data) {
            if (empty($data['search_value'])) {
                continue;
            }

            $this->filterAggregator->getFilter($key)
                ->attachTo($data['search_value'], $data['condition'], $queryBuilder);
        }

        $navigationTimings = $queryBuilder->getQuery()
            ->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);


        /** @var NavigationTimings $nav */
        foreach ($navigationTimings as $nav) {
            if ($nav[$perfMetricCamelized] > 150) {
                $samples[] = $nav[$perfMetricCamelized];
            }
        }

        return $samples;
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

}