<?php

declare(strict_types=1);

namespace App\BasicRum\Visit\Calculator\Aggregator;

class Duration
{

    /**
     * @param array $firstPageView
     * @param array $lastPageView
     * @return int
     */
    public function calculatePageViewsDurationDuration(array $firstPageView, array $lastPageView) : int
    {
        return $lastPageView['createdAt']->getTimestamp() - $firstPageView['createdAt']->getTimestamp();
    }

}