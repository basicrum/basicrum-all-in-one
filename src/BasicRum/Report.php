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
     * @param array $data
     * @param string $perfMetric
     * @return array
     */
    public function query(array $period, string $perfMetric)
    {
        $dayInterval = new DayInterval();

        $interval = $dayInterval->generateDayIntervals(
            $period['current_period_from_date'],
            $period['current_period_to_date']
        );

        $samples = [];

        foreach ($interval as $day)
        {
            $cacheKey = 'fdf3' . md5($day['start'] . $day['end'] . $perfMetric);

            if ($this->cache->has($cacheKey)) {
                $samples = array_merge($samples, $this->cache->get($cacheKey));
            } else {
                $cachedSamples = $this->_getInMetricInPeriod($day['start'], $day['end'], $perfMetric);
                $this->cache->set($cacheKey, $cachedSamples);
                $samples = array_merge($samples, $cachedSamples);
            }
        }

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