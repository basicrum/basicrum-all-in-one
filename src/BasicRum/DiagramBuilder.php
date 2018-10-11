<?php

namespace App\BasicRum;

use App\BasicRum\Date\DayInterval;

class DiagramBuilder
{

    public function build($data)
    {
        $dayInterval = new DayInterval();

        $interval = $dayInterval->generateDayIntervals($data['current_period_from_date'], $data['current_period_to_date']);

        return $interval;
    }

}