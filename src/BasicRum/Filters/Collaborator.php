<?php

declare(strict_types=1);

namespace App\BasicRum\Filters;

class Collaborator implements \App\BasicRum\CollaboratorsInterface
{

    /** @var array */
    private $filtersClassMap = [
        'browser_name'        => Secondary\BrowserName::class,
        'device_type'         => Secondary\DeviceType::class,
        'device_manufacturer' => Secondary\DeviceManufacturer::class,
        'os_name'             => Secondary\OsName::class,
        'url'                 => Secondary\Url::class,
        'time_to_first_byte'  => Primary\TimeToFirstByte::class,
        'time_to_first_paint' => Primary\TimeToFirstPaint::class
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