<?php

declare(strict_types=1);

namespace App\BasicRum\DiagramSchema\BusinessMetrics;

class Collaborator implements \App\BasicRum\DiagramSchema\CollaboratorsInterface
{
    /** @var array */
    private $businessMetricsClassMap = [
        'bounce_rate' => BounceRate::class,
        'stay_on_page_time' => StayOnPageTyme::class,
        'page_views_count' => PageViewsCount::class,
    ];

    public function getAllPossibleMetrics(): array
    {
        return $this->businessMetricsClassMap;
    }
}
