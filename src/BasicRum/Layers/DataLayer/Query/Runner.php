<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class Runner
{

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var array */
    private $planActions = [];

    /** @var FilesystemAdapter */
    private $cacheAdapter;

    /** @var Runner\SecondaryFilter */
    private $secondaryFilter;

    /** @var Runner\ComplexSelect */
    private $complexSelect;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry, array $planActions)
    {
        $this->registry     = $registry;
        $this->planActions  = $planActions;
        $this->cacheAdapter = new FilesystemAdapter('basicrum.datalayer.runner.cache', 300);

        $this->secondaryFilter = new Runner\SecondaryFilter($registry, $this->cacheAdapter);
        $this->complexSelect   = new Runner\ComplexSelect($registry, $this->cacheAdapter);
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function run() : array
    {
        $repository = $this->registry->getRepository($this->getEntityClassName($this->planActions['main_entity_name']));

        $limitFilters = $this->_processPrefetchFilters($this->planActions['where']['limitFilters']);

        // Abort if we do not have limit result in day
        if(empty($limitFilters)) {
            return [];
        }

        // Complex Select case
        $complexSelectData = [];
        $complexSelectFilters = [];

        /** @var \App\BasicRum\Layers\DataLayer\Query\Plan\ComplexSelect $complexSelect */
        foreach ($this->planActions['complex_selects'] as $complexSelect) {
            $complexSelectData = $this->_processComplexSelect($complexSelect, $limitFilters);
            if (!empty($complexSelectData)) {
                $complexSelectFilters[] = $this->planActions['main_entity_name'] . ".pageViewId"  .  " " .  ' IN(' . implode(',', array_keys($complexSelectData)) . ')';
            }
        }

        $filters = $this->_processPrefetchFilters($this->planActions['where']['secondaryFilters']);

        $filters = array_merge($limitFilters, $filters, $complexSelectFilters);

        $queryBuilder = $repository->createQueryBuilder($this->planActions['main_entity_name']);

        foreach ($filters as $filter) {
            $queryBuilder->andWhere($filter);
        }

        /** @var \App\BasicRum\Layers\DataLayer\Query\Plan\PrimaryFilter $primaryFilter */
        foreach ($this->planActions['where']['primaryFilters'] as $primaryFilter) {
            $queryBuilder->andWhere($primaryFilter->getCondition()->getWhere());

            $params = $primaryFilter->getCondition()->getParams();
            foreach ($params as $name => $value) {
                $queryBuilder->setParameter($name, $value);
            }
        }

        $selects = [];

        /** @var \App\BasicRum\Layers\DataLayer\Query\Plan\Select $select */
        foreach ($this->planActions['selects'] as $select) {
            $selects[] = $select->getSelect()->getFields()[0];
        }

        $queryBuilder->select($selects);

//        var_dump($queryBuilder->getQuery()->getSQL());

        $res = $queryBuilder->getQuery()->getResult();

        if (!empty($complexSelectData)) {
            foreach ($res as $key => $row) {
                $res[$key] = array_merge($row, $complexSelectData[$row[$complexSelect->getPrimaryKeyFieldName()]]);
            }
        }

        if (strpos(print_r($selects, true),'COUNT(') !== false) {
            return [
                [
                    'count' => $res[0][1]
                ]
            ];
        }

        return $res;
    }

    /**
     * @param Plan\ComplexSelect $complexSelect
     * @param $filters array
     * @return array
     */
    private function _processComplexSelect(Plan\ComplexSelect $complexSelect, array $filters) : array
    {
        return $this->complexSelect->process($complexSelect, $filters);
    }

    /**
     * @param array $filters
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function _processPrefetchFilters(array $filters) : array
    {
        return $this->secondaryFilter->process($filters);
    }

    /**
     * @param string $className
     * @return string
     */
    public function getEntityClassName(string $className) : string
    {
        return '\App\Entity\\' . $className;
    }

}