<?php

declare(strict_types=1);

namespace App\BasicRum\BusinessMetrics;

class Collaborator implements \App\BasicRum\CollaboratorsInterface
{
    /** @var array */
    private $businessMetricsClassMap = [
        'bounce_rate' => BounceRate::class,
        'stay_on_page_time' => StayOnPageTyme::class,
        'page_views_count' => PageViewsCount::class,
    ];

    /** @var array */
    private $businessMetrics = [];

    public function getCommandParameterName(): string
    {
        return 'business_metrics';
    }

    public function applyForRequirement(array $requirements): \App\BasicRum\CollaboratorsInterface
    {
        foreach ($this->businessMetricsClassMap as $filterKey => $class) {
            if (isset($requirements[$filterKey])) {
                $requirement = $requirements[$filterKey];

                if (1 == $requirement) {
                    /** @var \App\BasicRum\Report\ComplexSelectableInterface $businessMetric */
                    $businessMetric = new $class();

                    $this->businessMetrics[$filterKey] = $businessMetric;
                }
            }
        }

        return $this;
    }

    public function getRequirements(): array
    {
        return $this->businessMetrics;
    }

    public function getAllPossibleRequirementsKeys(): array
    {
        return array_keys($this->businessMetricsClassMap);
    }

    public function getAllPossibleRequirements(): array
    {
        return $this->businessMetricsClassMap;
    }
}
