<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query;

use App\BasicRum\Cache\Storage;

class Runner
{

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var array */
    private $planActions = [];

    /** @var Storage */
    private $cacheAdapter;

    /** @var Runner\SecondaryFilter */
    private $secondaryFilter;

    /** @var Runner\ComplexSelect */
    private $complexSelect;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry, array $planActions)
    {
        $this->registry     = $registry;
        $this->planActions  = $planActions;
        $this->cacheAdapter = new Storage('basicrum.datalayer.runner.cache', 300);

        $this->secondaryFilter = new Runner\SecondaryFilter($registry, $this->cacheAdapter);
        $this->complexSelect   = new Runner\ComplexSelect($registry, $this->cacheAdapter);
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function run() : array
    {
        $limitFilters = $this->_processPrefetchFilters($this->planActions['where']['limitFilters'], []);

        $whereArr = [];

        // Abort if we do not have limit result in day
        if(empty($limitFilters)) {
            return [];
        }

        /** @var \App\BasicRum\Layers\DataLayer\Query\Plan\PrimaryFilter $primaryFilter */
        foreach ($this->planActions['where']['primaryFilters'] as $primaryFilter) {
            $whereArr[] = $primaryFilter->getCondition()->getWhere();
        }

        /**
         * @todo: send $limitFilters as param for secondary filters and decide how to proceed in
         * App\BasicRum\Layers\DataLayer\Query\Runner\SecondaryFilter
         */
        list($complexSelectsResults, $complexSelectFilters) = $this->_processComplexSelect($limitFilters);

        $filters = $this->_processPrefetchFilters($this->planActions['where']['secondaryFilters'], $limitFilters);

        if (empty($filters) && !empty($this->planActions['where']['secondaryFilters'])) {
            return [];
        }

        $filters = array_merge($filters, $complexSelectFilters);

        foreach ($filters as $filter) {
            $whereArr[] = $filter;
        }

        //Playing a bit with generating low level query
        $connection = $this->registry->getConnection();

        $sqlWhere = implode(' AND ', $whereArr);
        /** @var \App\BasicRum\Layers\DataLayer\Query\Plan\PrimaryFilter $primaryFilter */
        foreach ($this->planActions['where']['primaryFilters'] as $primaryFilter) {
            $params = $primaryFilter->getCondition()->getParams();

            foreach ($params as $search => $replace) {
                $r = '\'' . $replace . '\'';
                $sqlWhere = str_replace(  ':' . $search, $r, $sqlWhere);
            }
        }

        $res = $this->planActions['data_flavor']->retrieve($connection, $sqlWhere, $limitFilters);

        if (!empty($complexSelectsResults)) {
            foreach ($complexSelectsResults as $complexSelectKey => $complexSelectData) {
                foreach ($res as $key => $row) {
                    $res[$key] = array_merge($row, $complexSelectData[$row[$this->planActions['complex_selects'][$complexSelectKey]->getPrimaryKeyFieldName()]]);
                }
            }
        }

        return $res;
    }

    /**
     * @param $filters array
     * @return array
     */
    private function _processComplexSelect(array $filters) : array
    {
        // Complex Select case
        $complexSelectFilters = [];
        $complexSelectData    = [];

        /** @var \App\BasicRum\Layers\DataLayer\Query\Plan\ComplexSelect $complexSelect */
        foreach ($this->planActions['complex_selects'] as $complexSelect) {
            $complexSelectData[] = $this->complexSelect->process($complexSelect, $filters);
            if (!empty($complexSelectData)) {
                foreach ($complexSelectData as $data) {
                    $complexSelectFilters[] = $this->planActions['main_table_name'] . ".page_view_id"  .  " " .  ' IN(' . implode(',', array_keys($data)) . ')';
                }
            }
        }

        return [$complexSelectData, $complexSelectFilters];
    }

    /**
     * @param array $filters
     * @param array $limitFilters
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function _processPrefetchFilters(array $filters, array $limitFilters) : array
    {
        return $this->secondaryFilter->process($filters, $limitFilters);
    }

}