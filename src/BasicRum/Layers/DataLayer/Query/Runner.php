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

        $queryBuilder = $repository->createQueryBuilder($this->planActions['main_entity_name']);

        foreach ($limitFilters as $filter) {
            $queryBuilder->andWhere($filter);
        }

        // Abort if we do not have limit result in day
        if(empty($limitFilters)) {
            return [];
        }

        list($complexSelectData, $complexSelectFilters) = $this->_processComplexSelect($limitFilters);

        $filters = $this->_processPrefetchFilters($this->planActions['where']['secondaryFilters']);

        $filters = array_merge($filters, $complexSelectFilters);

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
                $res[$key] = array_merge($row, $complexSelectData[$row[$this->planActions['complex_selects'][0]->getPrimaryKeyFieldName()]]);
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
            $complexSelectData = $this->complexSelect->process($complexSelect, $filters);
            if (!empty($complexSelectData)) {
                $complexSelectFilters[] = $this->planActions['main_entity_name'] . ".pageViewId"  .  " " .  ' IN(' . implode(',', array_keys($complexSelectData)) . ')';
            }
        }

        return [$complexSelectData, $complexSelectFilters];
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