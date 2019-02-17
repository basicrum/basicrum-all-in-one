<?php

declare(strict_types=1);

namespace App\BasicRum\TechnicalMetrics;

class Collaborator implements \App\BasicRum\CollaboratorsInterface
{

    /** @var array */
    private $technicalMetricsClassMap = [
        'time_to_first_paint' => TimeToFirstPaint::class
    ];

    private $technicalMetrics = [];

    public function getCommandParameterName() : string
    {
        return 'technical_metrics';
    }

    public function applyForRequirement(array $requirements) : \App\BasicRum\CollaboratorsInterface
    {
        foreach ($this->technicalMetricsClassMap as $filterKey => $class) {
            if (isset($requirements[$filterKey])) {
                $requirement = $requirements[$filterKey];

                if ($requirement == 1) {
                    continue;
                }

                /** @var \App\BasicRum\Report\SelectableInterface $filter */
                $filter = new $class($requirement['condition'], $requirement['search_value']);

                $this->technicalMetrics[$filterKey] = $filter;
            }
        }

        return $this;
    }

    public function getRequirements() : array
    {
        return $this->technicalMetrics;
    }

}