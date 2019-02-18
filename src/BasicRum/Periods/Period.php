<?php

declare(strict_types=1);

namespace App\BasicRum\Periods;

use App\BasicRum\Date\DayInterval;

class Period
    implements
    \App\BasicRum\Report\PeriodicFilterableInterface
{

    /** @var string */
    private $startPeriod;

    /** @var string */
    private $endPeriod;

    /** @var array */
    private $intervals = [];

    /** @var int */
    private $currentInterval = 0;

    /** @var int */
    private $intervalsCount = 0;

    /**
     * @param string $startPeriod
     * @param string $endPeriod
     * @return \App\BasicRum\Report\PeriodicFilterableInterface
     */
    public function setPeriod(string $startPeriod, string $endPeriod) : \App\BasicRum\Report\PeriodicFilterableInterface
    {
        $this->startPeriod = $startPeriod;
        $this->endPeriod   = $endPeriod;

        $this->generateIntervals();
        $this->intervalsCount = count($this->intervals);
        $this->currentInterval = 0;

        return $this;
    }

    private function generateIntervals()
    {
        $dayInterval = new DayInterval();
        $intervals = $dayInterval->generateDayIntervals($this->startPeriod, $this->endPeriod);

        foreach ($intervals as $interval) {
            $this->intervals[] = new PeriodInterval($interval['start'], $interval['end']);
        }
    }

    /**
     * @return string
     */
    public function getDataField() : string
    {
        return 'created_at';
    }

    /**
     * @return string
     */
    public function getEntity() : string
    {
        return 'NavigationTimings';
    }

    /**
     * @return \App\BasicRum\Periods\PeriodInterval
     */
    public function requestPeriodInterval() : \App\BasicRum\Periods\PeriodInterval
    {
        $interval = $this->intervals[$this->currentInterval];

        $this->currentInterval++;

        return $interval;
    }

    /**
     * @return bool
     */
    public function hasPeriods() : bool
    {
        return $this->currentInterval < $this->intervalsCount;
    }

}