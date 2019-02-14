<?php

declare(strict_types=1);

namespace App\BasicRum\Filters;

class Collaborator implements \App\BasicRum\CollaboratorsInterface
{

    /** @var array */
    private $filtersClassMap = [
        'browser_name' => Metric\BrowserName::class,
        'device_type'  => Metric\DeviceType::class,
        'os_name'      => Metric\OsName::class,
        'url'          => Metric\Url::class,
    ];

    private $filters = [];

    public function getCommandParameterName() : string
    {
        return 'filters';
    }

    public function applyForRequirement(array $requirements) : \App\BasicRum\CollaboratorsInterface
    {
        foreach ($this->filtersClassMap as $filterKey => $class) {
            if (isset($requirements[$filterKey])) {
                $requirement = $requirements[$filterKey];

                if (empty($requirement['search_value'])) {
                    continue;
                }

                /** @var Metric\AbstractFilter $collaborator */
                $filter = new $class($requirement['condition'], $requirement['search_value']);


                $this->filters[$filterKey] = $filter;
            }
        }

        return $this;
    }

    public function getRequirements() : array
    {
        return $this->filters;
    }

}