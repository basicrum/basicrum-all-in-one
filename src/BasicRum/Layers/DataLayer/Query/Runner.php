<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query;

class Runner
{

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var array */
    private $planActions = [];

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry, array $planActions)
    {
        $this->registry    = $registry;
        $this->planActions = $planActions;
    }

    /**
     * @return array
     */
    public function run() : array
    {
        $repository = $this->registry->getRepository($this->getEntityClassName($this->planActions['main_entity_name']));

        $filters = array_merge($this->_processPrefetchFilters());

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

        $queryBuilder->select([$this->planActions['main_entity_name'] . '.pageViewId']);

        $res = $queryBuilder->getQuery()->getResult();

        return $res;
    }

    /**
     * @return array
     */
    private function _processPrefetchFilters() : array
    {
        $res = [];

        // Concept for prefetch filter
        /** @var \App\BasicRum\Layers\DataLayer\Query\Plan\SecondaryFilter $prefetchCondition */
        foreach ($this->planActions['where']['secondaryFilters'] as $prefetchCondition) {

            $where = $prefetchCondition->getPrefetchCondition()->getWhere();
            $params = $prefetchCondition->getPrefetchCondition()->getParams();

            $selectFields = $prefetchCondition->getPrefetchSelect()->getFields();

            $repository = $this->registry
                ->getRepository($this->getEntityClassName($prefetchCondition->getPrimaryEntityName()));

            if ($prefetchCondition->getMainCondition() === 'IN') {
                $repository = $this->registry
                    ->getRepository($this->getEntityClassName('NavigationTimingsUserAgents'));
            }

            $queryBuilder = $repository->createQueryBuilder($prefetchCondition->getPrimaryEntityName());

            if ($prefetchCondition->getMainCondition() === 'IN') {
                $queryBuilder = $repository->createQueryBuilder('NavigationTimingsUserAgents');
            }

            $queryBuilder->where($where);

            foreach ($params as $name => $value) {
                $queryBuilder->setParameter($name, $value);
            }

            $queryBuilder->select($selectFields);

            if ($prefetchCondition->getMainCondition() === 'IN') {
                $fetched = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_SCALAR);

                //@todo: Maybe better to use custom hydrator https://stackoverflow.com/a/27823082/1016533
                $ids = array_column($fetched, "id");
                $res[] = $prefetchCondition->getPrimaryEntityName() . "."  . $prefetchCondition->getPrimarySearchFieldName() .  " " .  ' IN(' . implode(',', $ids) . ')';
            } else {
                // If not MIN or MAX then we need get the result in array
                $fetched = $queryBuilder->getQuery()->getSingleScalarResult();
                $res[] = $prefetchCondition->getPrimaryEntityName() . "."  . $prefetchCondition->getPrimarySearchFieldName() .  " " . $prefetchCondition->getMainCondition() .  ' ' . $fetched;
            }
        }

        return $res;
    }

    /**
     * @return array
     */
    private function _processPrimaryFilter() : array
    {
        $res = [];

        /** @var \App\BasicRum\Layers\DataLayer\Query\Plan\PrimaryFilter $primaryFilter */
        foreach ($this->planActions['where']['primaryFilters'] as $primaryFilter) {
            $res[] = $primaryFilter->getCondition()->getWhere();
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

    /**
     * @param $entityName
     * @return string
     */
    public function getEntityNamePrefix($entityName) : string
    {
        return strtolower(preg_replace('/[a-z]/', '$1', $entityName));
    }

}