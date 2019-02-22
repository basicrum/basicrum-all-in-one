<?php

declare(strict_types=1);

namespace App\BasicRum\BusinessMetrics;

class Collaborator
    implements \App\BasicRum\CollaboratorsInterface
{

    /** @var array */
    private $businessMetricsClassMap = [
        'bounce_rate'       => BounceRate::class,
        'stay_on_page_time' => StayOnPageTyme::class
    ];

    /** @var array */
    private $businessMetrics = [];

    /**
     * @return string
     */
    public function getCommandParameterName() : string
    {
        return 'business_metrics';
    }

    /**
     * @param array $requirements
     * @return \App\BasicRum\CollaboratorsInterface
     */
    public function applyForRequirement(array $requirements) : \App\BasicRum\CollaboratorsInterface
    {
        foreach ($this->businessMetricsClassMap as $filterKey => $class) {
            if (isset($requirements[$filterKey])) {
                $requirement = $requirements[$filterKey];

                if ($requirement == 1) {
                    /** @var \App\BasicRum\Report\ComplexSelectableInterface $businessMetric */
                    $businessMetric = new $class();

                    $this->businessMetrics[$filterKey] = $businessMetric;
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRequirements() : array
    {
        return $this->businessMetrics;
    }


    /**
     * @return array
     */
    public function getAllPossibleRequirementsKeys() : array
    {
        return array_keys($this->businessMetricsClassMap);
    }

}