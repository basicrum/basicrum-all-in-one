<?php

declare(strict_types=1);

namespace App\BasicRum\Periods;

class PeriodInterval
{

    /** @var string */
    private $startInterval;

    /** @var string */
    private $endInterval;

    /**
     * @param string $startInterval
     * @param string $endInterval
     */
    public function __construct(string $startInterval, string $endInterval)
    {
        $this->startInterval = $startInterval;
        $this->endInterval   = $endInterval;
    }

    /**
     * @return string
     */
    public function getStartInterval()
    {
        return $this->startInterval;
    }

    /**
     * @return string
     */
    public function getEndInterval()
    {
        return $this->endInterval;
    }

}