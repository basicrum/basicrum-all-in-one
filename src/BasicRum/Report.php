<?php

declare(strict_types=1);

namespace App\BasicRum;

use Doctrine\ORM\EntityManager;

use App\BasicRum\Date\DayInterval;
use App\BasicRum\NavigationTiming\MetricAggregator;

use App\Entity\PageTypeConfig;
use App\Entity\NavigationTimings;
use App\Entity\ResourceTimings;
use Symfony\Component\Cache\Simple\FilesystemCache;

class Report
{

    /* @var \Doctrine\Bundle\DoctrineBundle\Registry $em */
    protected $em;

    /** @var \Symfony\Component\Cache\Simple\FilesystemCache */
    protected $cache;

    /**
     * OneLevel Constructor
     *
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $em
     */
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $em)
    {
        $this->em = $em;
        $this->cache = new FilesystemCache();
    }

    /**
     * @param array $period
     * @param string $perfMetric
     * @return array
     */
    public function query(array $period, string $perfMetric)
    {
        $cacheKey = 'fdf37' . md5($period['start'] . $period['end'] . $perfMetric);

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $samples = $this->_getInMetricInPeriod($period['start'], $period['end'], $perfMetric);
        $this->cache->set($cacheKey, $samples);

        return $samples;
    }

    /**
     * @param string $start
     * @param string $end
     * @param string $perfMetric
     * @return array
     */
    private function _getInMetricInPeriod($start, $end, $perfMetric)
    {
        $samples = [];

        $repository = $this->em->getRepository(NavigationTimings::class);

        $query = $repository->createQueryBuilder('nt')
            ->where("nt.createdAt BETWEEN '" . $start . "' AND '" . $end . "'")
            ->getQuery();

        $navigationTimings = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        /**
         * Crazy way to construct array key
         *
         * 'nt_res_st' -> 'ntResSt'
         *
         */
        $perfMetricCamelized = lcfirst(str_replace(" ", "",ucwords(str_replace("_", " ", $perfMetric))));

        /** @var NavigationTimings $nav */
        foreach ($navigationTimings as $nav) {
            $samples[] = $nav[$perfMetricCamelized] - $nav['ntNavSt'];
        }

        return $samples;
    }

    /**
     * @return array
     */
    public function getNavigationTimings()
    {
        $metricAggregator = new MetricAggregator();
        return $metricAggregator->getMetrics();
    }

}