<?php

declare(strict_types=1);

namespace App\BasicRum\Layers;

use App\BasicRum\Cache\Storage;
use App\BasicRum\Layers\DataLayer\Query\Planner;
use App\BasicRum\Layers\DataLayer\Query\Runner;
use App\BasicRum\Periods\Period;

/**
 * Class DataLayer.
 */
class DataLayer
{
    /** @var Period */
    private $period;

    /** @var \App\BasicRum\Layers\DataLayer\Query\MainDataSelect\MainDataInterface */
    private $mainDataSelect;

    /** @var array */
    private $dataRequirements = [];

    /** @var Runner */
    private $runner;

    /**
     * DataLayer constructor.
     */
    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    /**
     * @return DataLayer
     */
    public function load(
        Period $period,
        array $dataRequirements,
        DataLayer\Query\MainDataSelect\MainDataInterface $mainDataSelect
    ) {
        $this->period = $period;
        $this->dataRequirements = $dataRequirements;
        $this->mainDataSelect = $mainDataSelect;

        return $this;
    }

    /**
     * @return array
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function process()
    {
        $res = [];

        /** todo: Inject the cache Adapter in different way in order to support different cache storage */
        /** todo: Check if this affects the result of PHP unit, we do not want always to return cached results */
        $cache = new Storage('basicrum.report.cache');

        while ($this->period->hasPeriods()) {
            $interval = $this->period->requestPeriodInterval();

            $dbUrlArr = explode('/', $_ENV['DATABASE_URL']);

            /** todo: Think about adding a tag that at least can invalidate cache for certain day in interval */
            $cacheKey = end($dbUrlArr).'query_data_layer_'.md5($interval->getStartInterval().$interval->getEndInterval().print_r($this->dataRequirements, true));

            $cacheKey .= $this->mainDataSelect->getCacheKeyPart();

            if (true && $cache->hasItem($cacheKey)) {
                $res[$interval->getStartInterval()] = $cache->getItem($cacheKey)->get();
                continue;
            }

            $queryPlanner = new Planner(
                $interval->getStartInterval(),
                $interval->getEndInterval(),
                $this->dataRequirements,
                $this->mainDataSelect
            );

            $planActions = $queryPlanner->createPlan()->releasePlan();

            $data = $this->runner->load($planActions)->run();

            $cacheItem = $cache->getItem($cacheKey);
            $cacheItem->set($data);

            $cache->save($cacheItem);

            $res[$interval->getStartInterval()] = $data;
        }

        return $res;
    }
}
