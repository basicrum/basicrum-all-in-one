<?php

declare(strict_types=1);

namespace App\BasicRum\TechnicalMetrics;

class Collaborator implements \App\BasicRum\CollaboratorsInterface
{

    /** @var array */
    private $technicalMetricsClassMap = [
        'time_to_first_paint' => TimeToFirstPaint::class,
        'document_ready'      => DocumentReady::class,
        'time_to_first_byte'  => TimeToFirstByte::class,
    ];

    private $technicalMetrics = [];

    public function getCommandParameterName() : string
    {
        return 'technical_metrics';
    }

    /**
     * @param array $requirements
     * @return \App\BasicRum\CollaboratorsInterface
     */
    public function applyForRequirement(array $requirements) : \App\BasicRum\CollaboratorsInterface
    {
        foreach ($this->technicalMetricsClassMap as $filterKey => $class) {
            if (isset($requirements[$filterKey])) {
                $requirement = $requirements[$filterKey];

                if ($requirement == 1) {
                    /** @var \App\BasicRum\Report\SelectableInterface $filter */
                    $filter = new $class();

                    $this->technicalMetrics[$filterKey] = $filter;
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
        return $this->technicalMetrics;
    }

    /**
     * @return array
     */
    public function getAllPossibleRequirementsKeys() : array
    {
        return array_keys($this->technicalMetricsClassMap);
    }

}