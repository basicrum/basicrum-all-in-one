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

    private $cacheAdapter;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry, array $planActions)
    {
        $this->registry     = $registry;
        $this->planActions  = $planActions;
        $this->cacheAdapter = new FilesystemAdapter('basicrum.report.cache', 300);
    }

    private function getPrefetcCacheKey(\App\BasicRum\Layers\DataLayer\Query\ConditionInterface $condition) {
        return 'prefetch_condition_query_data_layer_' .
            md5($condition->getWhere() . print_r($condition->getParams(), true));
    }

    /**
     * @return array
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
            $selects[] = $select->getEntityName() . '.' . $select->getDataFieldName();
        }

        $queryBuilder->select($selects);

//        var_dump($queryBuilder->getQuery()->getSQL());

        $res = $queryBuilder->getQuery()->getResult();

        if (!empty($complexSelectData)) {
            foreach ($res as $key => $row) {
                $res[$key] = array_merge($row, $complexSelectData[$row[$complexSelect->getPrimaryKeyFieldName()]]);
            }
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
        $repository = $this->registry
            ->getRepository($this->getEntityClassName($complexSelect->getSecondarySelectEntityName()));

        $queryBuilder = $repository->createQueryBuilder($complexSelect->getSecondarySelectEntityName());

        $selects = [];

        /** @var \App\BasicRum\Layers\DataLayer\Query\Plan\Select $select */
        foreach ($complexSelect->getSecondarySelectDataFieldNames() as $field) {
            $selects[] = $complexSelect->getSecondarySelectEntityName() . '.' . $field;
        }

        foreach ($filters as $filter) {
            $corrected = str_replace(
                $complexSelect->getPrimarySelectEntityName() . '.' . $complexSelect->getPrimaryKeyFieldName(),
                $complexSelect->getSecondarySelectEntityName()  . '.' . $complexSelect->getSecondaryKeyFieldName(),
                $filter
            );
            $queryBuilder->andWhere($corrected);
        }

        $queryBuilder->select($selects);

        $res = $queryBuilder->getQuery()->getResult();

        $data = [];

        // We need $complexSelect->getSecondaryKeyFieldName() as a key in new array because we will use it later for IN clause
        foreach ($res as $row) {
            $data[$row[$complexSelect->getSecondaryKeyFieldName()]] = $row;
        }

        return $data;
    }

    /**
     * @param $filters array
     * @return array
     */
    private function _processPrefetchFilters(array $filters) : array
    {
        $res = [];

        // Concept for prefetch filter
        /** @var \App\BasicRum\Layers\DataLayer\Query\Plan\SecondaryFilter $prefetchCondition */
        foreach ($filters as $prefetchCondition) {
            $cacheKey = $this->getPrefetcCacheKey($prefetchCondition->getPrefetchCondition());

            $where = $prefetchCondition->getPrefetchCondition()->getWhere();
            $params = $prefetchCondition->getPrefetchCondition()->getParams();

            $selectFields = $prefetchCondition->getPrefetchSelect()->getFields();

            $repository = $this->registry
                ->getRepository($this->getEntityClassName($prefetchCondition->getPrimaryEntityName()));

            if (in_array($prefetchCondition->getMainCondition(), ['is', 'isNot', 'contains'])) {
                $repository = $this->registry
                    ->getRepository($this->getEntityClassName($prefetchCondition->getSecondaryEntityName()));
            }

            $queryBuilder = $repository->createQueryBuilder($prefetchCondition->getPrimaryEntityName());

            if (in_array($prefetchCondition->getMainCondition(), ['is', 'isNot', 'contains'])) {
                $queryBuilder = $repository->createQueryBuilder($prefetchCondition->getSecondaryEntityName());
            }

            $queryBuilder->where($where);

            foreach ($params as $name => $value) {
                $queryBuilder->setParameter($name, $value);
            }

            $queryBuilder->select($selectFields);

            if ($prefetchCondition->getMainCondition() === 'is' || $prefetchCondition->getMainCondition() === 'contains') {

                if ($this->cacheAdapter->hasItem($cacheKey)) {
                    $fetched =  $this->cacheAdapter->getItem($cacheKey)->get();
                } else {
                    $fetched = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_SCALAR);
                    $cacheItem = $this->cacheAdapter->getItem($cacheKey);
                    $cacheItem->set($fetched);
                    $this->cacheAdapter->save($cacheItem);
                }

                if(empty($fetched)) {
                    continue;
                }

                //@todo: Maybe better to use custom hydrator https://stackoverflow.com/a/27823082/1016533
                $ids = array_column($fetched, "id");
                $res[] = $prefetchCondition->getPrimaryEntityName() . "."  . $prefetchCondition->getPrimarySearchFieldName() .  " " .  ' IN(' . implode(',', $ids) . ')';
            } elseif ($prefetchCondition->getMainCondition() === 'isNot') {

                if ($this->cacheAdapter->hasItem($cacheKey)) {
                    $fetched =  $this->cacheAdapter->getItem($cacheKey)->get();
                } else {
                    $fetched = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_SCALAR);
                    $cacheItem = $this->cacheAdapter->getItem($cacheKey);
                    $cacheItem->set($fetched);
                    $this->cacheAdapter->save($cacheItem);
                }

                if(empty($fetched)) {
                    continue;
                }

                //@todo: Maybe better to use custom hydrator https://stackoverflow.com/a/27823082/1016533
                $ids = array_column($fetched, "id");
                $res[] = $prefetchCondition->getPrimaryEntityName() . "."  . $prefetchCondition->getPrimarySearchFieldName() .  " " .  'NOT IN(' . implode(',', $ids) . ')';
            }
            else {
                // If not MIN or MAX then we need get the result in array
                $fetched = $queryBuilder->getQuery()->getSingleScalarResult();
                if(empty($fetched)) {
                    continue;
                }
                $res[] = $prefetchCondition->getPrimaryEntityName() . "."  . $prefetchCondition->getPrimarySearchFieldName() .  " " . $prefetchCondition->getMainCondition() .  ' ' . $fetched;
            }
        }

        return $res;
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