<?php

declare(strict_types=1);

namespace App\BasicRum\Layers;

use App\BasicRum\Layers\DataLayer\Query\Planner;
use App\BasicRum\Layers\DataLayer\Query\Runner;

class DataLayer
{

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var \App\BasicRum\CollaboratorsInterface */
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
     */
    public function process()
    {
        $res = [];

        while ($this->period->hasPeriods()) {
            $interval = $this->period->requestPeriodInterval();

            $queryPlanner = new Planner(
                $interval->getStartInterval(),
                $interval->getEndInterval(),
                $this->dataRequirements
            );

            $planActions = $queryPlanner->createPlan()->releasePlan();



            $planRunner = new Runner($this->registry, $planActions);

            $res[] = $planRunner->run();

        }

        dd($res);

        return [];
    }



}