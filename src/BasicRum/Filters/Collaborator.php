<?php

declare(strict_types=1);

namespace App\BasicRum\Filters;

class Collaborator implements \App\BasicRum\CollaboratorsInterface
{
    /** @var array */
    private $filtersClassMap = [
        'browser_name' => Secondary\BrowserName::class,
        'device_manufacturer' => Secondary\DeviceManufacturer::class,
        'url' => Secondary\Url::class,
        'device_type' => Primary\DeviceType::class,
        'operating_system' => Primary\OperatingSystem::class,
        'first_byte' => Primary\TimeToFirstByte::class,
        'first_paint' => Primary\TimeToFirstPaint::class,
        'query_param' => Secondary\QueryParam::class,
        'page_views_count' => Secondary\PageViewsCount::class,
    ];

    private $filters = [];

    public function getCommandParameterName(): string
    {
        return 'filters';
    }

    public function applyForRequirement(array $requirements): \App\BasicRum\CollaboratorsInterface
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

    public function getRequirements(): array
    {
        return $this->filters;
    }

    public function getAllPossibleRequirementsKeys(): array
    {
        return array_keys($this->filtersClassMap);
    }
}
