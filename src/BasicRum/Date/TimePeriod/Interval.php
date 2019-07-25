<?php

declare(strict_types=1);

namespace App\BasicRum\Date\TimePeriod;

class Interval
{

    private $start = '';

    private $end = '';

    /**
     * Interval constructor.
     * @param string $start
     * @param string $end
     */
    public function __construct(string $start, string $end)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    /**
     * @return string
     */
    public function getStart() : string
    {
        return $this->start;
    }

    /**
     * @return string
     */
    public function getEnd() : string
    {
        return $this->end;
    }

}