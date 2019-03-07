<?php

declare(strict_types=1);

namespace App\BasicRum\Filters;

class Collaborator implements \App\BasicRum\CollaboratorsInterface
{

    /** @var array */
    private $filtersClassMap = [
        'browser_name'        => Secondary\BrowserName::class,
        'device_manufacturer' => Secondary\DeviceManufacturer::class,
        'os_name'             => Secondary\OsName::class,
        'url'                 => Secondary\Url::class,
        'device_type'         => Primary\DeviceType::class,
        'time_to_first_byte'  => Primary\TimeToFirstByte::class,
        'time_to_first_paint' => Primary\TimeToFirstPaint::class
    ];

    private $filters = [];

    /**
     * @return string
     */
    public function getCommandParameterName() : string
    {
        return 'filters';
    }

    /**
     * @param array $requirements
     * @return \App\BasicRum\CollaboratorsInterface
     */
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

    /**
     * @return array
     */
    public function getRequirements() : array
    {
        return $this->filters;
    }

    /**
     * @return array
     */
    public function getAllPossibleRequirementsKeys() : array
    {
        return array_keys($this->filtersClassMap);
    }

}