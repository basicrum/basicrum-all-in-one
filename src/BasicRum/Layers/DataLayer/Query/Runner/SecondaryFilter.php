<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Runner;

class SecondaryFilter
{

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var \Symfony\Component\Cache\Adapter\FilesystemAdapter */
    private $cacheAdapter;

    public function __construct(
        \Doctrine\Bundle\DoctrineBundle\Registry $registry,
        \Symfony\Component\Cache\Adapter\FilesystemAdapter $cacheAdapter
    )
    {
        $this->registry     = $registry;
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * @param array $filters
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function process(array $filters) : array
    {
        $res = [];

        // Concept for prefetch filter
        /** @var \App\BasicRum\Layers\DataLayer\Query\Plan\SecondaryFilter $prefetchCondition */
        foreach ($filters as $prefetchCondition) {
            $cacheKey = $this->getPrefetchCacheKey($prefetchCondition->getPrefetchCondition());

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
                $fieldsArr = explode('.', $selectFields[0]);
                $mainColumn = end($fieldsArr);
                $ids = array_column($fetched, $mainColumn);
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
     * @param \App\BasicRum\Layers\DataLayer\Query\ConditionInterface $condition
     * @return string
     */
    private function getPrefetchCacheKey(\App\BasicRum\Layers\DataLayer\Query\ConditionInterface $condition) {
        $dbUrlArr = explode('/', getenv('DATABASE_URL'));

        return end($dbUrlArr) . 'prefetch_condition_query_data_layer_' .
            md5($condition->getWhere() . print_r($condition->getParams(), true));
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