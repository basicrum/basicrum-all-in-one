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
    public function run()
    {
        $repository = $this->registry->getRepository($this->getEntityClassName($this->planActions['main_entity_name']));

        $filters = $this->_processPrefetchFilters();

        $queryBuilder = $repository->createQueryBuilder($this->getEntityNamePrefix($this->planActions['main_entity_name']));



        return [$this->_processPrefetchFilters(), $this->planActions];
    }

    private function _processPrefetchFilters()
    {
        $res = [];

        // Concept for prefetch filter
        foreach ($this->planActions['where']['prefetch'] as $prefetch) {
            /** @var \App\BasicRum\Layers\DataLayer\Query\ConditionInterface $prefetchCondition */
            $prefetchCondition = $prefetch['prefetchCondition'];

            $where = $prefetchCondition->getWhere();
            $params = $prefetchCondition->getParams();

            /** @var \App\BasicRum\Layers\DataLayer\Query\SelectInterface $prefetchSelect */
            $prefetchSelect = $prefetch['prefetchSelect'];

            $selectFields = $prefetchSelect->getFields();

            $repository = $this->registry
                ->getRepository($this->getEntityClassName($this->planActions['main_entity_name']));

            $queryBuilder = $repository->createQueryBuilder($this->planActions['main_entity_name']);

            $queryBuilder->where($where);

            foreach ($params as $name => $value) {
                $queryBuilder->setParameter($name, $value);
            }

            $queryBuilder->select($selectFields[0]);

            $res[] = $queryBuilder->getQuery()->getSingleScalarResult();
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
    public function getEntityNamePrefix($entityName)
    {
        return strtolower(preg_replace('/[a-z]/', '$1', $entityName));
    }

}