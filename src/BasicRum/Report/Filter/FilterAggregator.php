<?php

declare(strict_types=1);

namespace App\BasicRum\Report\Filter;


class FilterAggregator
{

    /** @var array */
    private $filters = [];

    public function __construct()
    {
        $this->filters = [
            UserAgent::INTERNAL_IDENTIFIER => new UserAgent()
        ];
    }

    /**
     * @param $identifier
     * @return FilterInterface
     */
    public function getFilter($identifier)
    {
        return $this->filters[$identifier];
    }

}