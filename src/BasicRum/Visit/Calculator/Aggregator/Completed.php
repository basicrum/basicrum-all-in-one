<?php

declare(strict_types=1);

namespace App\BasicRum\Visit\Calculator\Aggregator;

class Completed
{
    /**
     * @return bool
     *
     * @throws \Exception
     */
    public function isVisitCompleted(\DateTime $earlier, \DateTime $later, int $duration)
    {
        // Check if there were no other visits in certain period of time
        $startDiff = strtotime($earlier->format('Y-m-d H:i:s'));
        $endDiff = strtotime($later->format('Y-m-d H:i:s'));

        $minutes = round(($endDiff - $startDiff) / 60, 2);

        if ($minutes < 0) {
            throw new \Exception('Visit duration can\'t be a negative number');
        }

        return $duration < $minutes;
    }
}
