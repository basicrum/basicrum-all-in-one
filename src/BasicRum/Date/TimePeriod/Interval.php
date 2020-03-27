<?php

declare(strict_types=1);

namespace App\BasicRum\Date\TimePeriod;

class Interval
{
    private $start = '';

    private $end = '';

    /**
     * Interval constructor.
     */
    public function __construct(string $start, string $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function getStart(): string
    {
        return $this->start;
    }

    public function getEnd(): string
    {
        return $this->end;
    }
}
