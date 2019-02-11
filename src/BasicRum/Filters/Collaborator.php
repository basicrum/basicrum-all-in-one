<?php

declare(strict_types=1);

namespace App\BasicRum\Filters;

class Collaborator implements \App\BasicRum\CollaboratorsInterface
{

    /** @var array */
    private $filtersClassMap = [
        Metric\BrowserName::class,
        Metric\DeviceType::class,
        Metric\OsName::class,
        Metric\Url::class,
    ];

    private $filters = [];

    public function getCommandParameterName() : string
    {
        return 'filters';
    }

    public function applyForRequirement(array $requirements) : \App\BasicRum\CollaboratorsInterface
    {
        foreach ($this->filtersClassMap as $class) {
            /** @var Metric\AbstractFilter $collaborator */
            $filter = new $class();
            if (isset($requirements[$filter->getDataField()])) {
                $requirement = $requirements[$filter->getDataField()];

                $filter->setCondition($requirement['condition']);
                $filter->setSearchValue($requirement['search_value']);

                $this->filters[$filter->getDataField()] = $filter;
            }
        }

        return $this;
    }

    public function getRequirements() : array
    {
        return $this->filters;
    }

}