<?php

declare(strict_types=1);

namespace App\BasicRum\Report;

interface PeriodicFilterableInterface
{

    /**
     * @param string $startPeriod
     * @param string $endPeriod
     */
    public function setPeriod(string $startPeriod, string $endPeriod) : self;

    public function getDataField() : string;

    public function getEntity() : string;

    public function requestPeriodInterval() : \App\BasicRum\Periods\PeriodInterval;

    public function hasPeriods() : bool;

}