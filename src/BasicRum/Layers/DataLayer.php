<?php

declare(strict_types=1);

namespace App\BasicRum\Layers;

use App\BasicRum\Layers\DataLayer\Query\Planner;
use App\BasicRum\Layers\DataLayer\Query\Runner;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class DataLayer
{

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var \App\BasicRum\Periods\Period */
    private $period;

    /** @var array */
    private $dataRequirements = [];

    /**
     * @todo: How to make it possible that we do not get Doctrine after passing the object in chain of couple of objects?
     *
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $registry
     * @param \App\BasicRum\Periods\Period $period
     * @param array $dataRequirements
     */
    public function __construct(
        \Doctrine\Bundle\DoctrineBundle\Registry $registry,
        \App\BasicRum\Periods\Period $period,
        array $dataRequirements
    )
    {
        $this->registry = $registry;
        $this->period = $period;
        $this->dataRequirements = $dataRequirements;

    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function process()
    {
        $res = [];

        /** todo: Inject the cache Adapter in different way in order to support different cache storage */
        /** todo: Check if this affects the result of PHP unit, we do not want always to return cached results */
        $cache = new FilesystemAdapter('basicrum.report.cache');

        while ($this->period->hasPeriods()) {
            $interval = $this->period->requestPeriodInterval();

            $dbUrlArr = explode('/', getenv('DATABASE_URL'));

            /** todo: Think about adding a tag that at least can invalidate cache for certain day in interval */
            $cacheKey = end($dbUrlArr) . 'query_data_layer_' . md5($interval->getStartInterval() . $interval->getEndInterval() . print_r($this->dataRequirements, true));

            if ($cache->hasItem($cacheKey)) {
                $res[$interval->getStartInterval()] =  $cache->getItem($cacheKey)->get();
                continue;
            }

            $queryPlanner = new Planner(
                $interval->getStartInterval(),
                $interval->getEndInterval(),
                $this->dataRequirements
            );

            $planActions = $queryPlanner->createPlan()->releasePlan();

            $planRunner = new Runner($this->registry, $planActions);

            $data = $planRunner->run();

            if (!empty($data)) {
                $cacheItem = $cache->getItem($cacheKey);
                $cacheItem->set($data);

                $cache->save($cacheItem);
            }

            $res[$interval->getStartInterval()] = $data;
        }

        return $res;
    }

}