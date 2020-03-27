<?php

declare(strict_types=1);

namespace App\BasicRum\Visit\Calculator\Aggregator;

class Duration
{
    public function calculatePageViewsDurationDuration(array $firstPageView, array $lastPageView): int
    {
        return $lastPageView['createdAt']->getTimestamp() - $firstPageView['createdAt']->getTimestamp();
    }
}
