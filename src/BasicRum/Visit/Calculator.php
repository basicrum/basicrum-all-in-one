<?php

declare(strict_types=1);

namespace App\BasicRum\Visit;

use App\BasicRum\Visit\Calculator\Aggregator;
use App\BasicRum\Visit\Data\Fetch;

class Calculator
{
    /** @var int */
    private $scannedChunkSize = 1000;

    /** @var int */
    private $sessionExpireMinutes = 30;

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var Fetch */
    private $fetch;

    /** @var Aggregator */
    private $aggregator;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry = $registry;
        $this->fetch = new Fetch($registry);
        $this->aggregator = new Aggregator($this->sessionExpireMinutes, $this->fetch);
    }

    public function calculate(): array
    {
        $lastPageViewId = $this->fetch->fetchPreviousLastScannedPageViewId();

        $navTimingsRes = $this->fetch->fetchNavTimingsInRange($lastPageViewId + 1, $lastPageViewId + $this->scannedChunkSize);

        $notCompletedVisits = $this->fetch->fetchNotCompletedVisits();

        foreach ($notCompletedVisits as $notCompletedVisit) {
            $notCompletedViews = $this->fetch->fetchNavTimingsInRangeForSession(
                $notCompletedVisit['firstPageViewId'],
                $notCompletedVisit['lastPageViewId'],
                $notCompletedVisit['rt_si']
            );

            foreach ($notCompletedViews as $view) {
                $this->aggregator->addPageView($view);
            }
        }

        foreach ($navTimingsRes as $nav) {
            $this->aggregator->addPageView($nav);
        }

        $visits = $this->aggregator->generateVisits($notCompletedVisits);

        return $visits;
    }
}
