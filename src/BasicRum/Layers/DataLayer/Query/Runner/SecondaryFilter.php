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
     * @param array $limitFilters
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function process(array $filters, array $limitFilters) : array
    {
        $res = [];

        // Concept for prefetch filter
        /** @var \App\BasicRum\Layers\DataLayer\Query\Plan\SecondaryFilter $prefetchCondition */
        foreach ($filters as $prefetchCondition) {
            $cacheKey = $this->getPrefetchCacheKey($prefetchCondition);

            $where = $prefetchCondition->getPrefetchCondition()->getWhere();
            $params = $prefetchCondition->getPrefetchCondition()->getParams();

            $selectFields = $prefetchCondition->getPrefetchSelect()->getFields();

            $repository = $this->registry
                ->getRepository($this->getEntityClassName($prefetchCondition->getSecondaryEntityName()));

            $queryBuilder = $repository->createQueryBuilder($prefetchCondition->getSecondaryEntityName());

            $queryBuilder->where($where);

            foreach ($params as $name => $value) {
                $queryBuilder->setParameter($name, $value);
            }

            if ($this->shouldLimitPrefetchCondition($prefetchCondition, $limitFilters)) {
                foreach ($limitFilters as $limitFilter) {
                    $transformed = str_replace(
                        $prefetchCondition->getPrimaryEntityName(),
                        $prefetchCondition->getSecondaryEntityName(),
                        $limitFilter
                    );

                    $queryBuilder->andWhere($transformed);
                }
            }

            $queryBuilder->select($selectFields);

            if ($this->cacheAdapter->hasItem($cacheKey)) {
                $fetched = $this->cacheAdapter->getItem($cacheKey)->get();
            } else {
                $fetched = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_SCALAR);
                $cacheItem = $this->cacheAdapter->getItem($cacheKey);
                $cacheItem->set($fetched);
                $this->cacheAdapter->save($cacheItem);
            }

            if (empty($fetched)) {
                continue;
            }

            if ($this->isMinMaxQuery($selectFields)) {
                $value = $this->getSingleRowValue($fetched);

                if (empty($value)) {
                    continue;
                }

                if ('>=' === $prefetchCondition->getMainCondition() || '<=' === $prefetchCondition->getMainCondition()) {
                    $res[] = $prefetchCondition->getPrimaryEntityName() . "."  . $prefetchCondition->getPrimarySearchFieldName() .  " " . $prefetchCondition->getMainCondition() .  ' ' . $value;
                }
                continue;
            }

            //@todo: Maybe better to use custom hydrator https://stackoverflow.com/a/27823082/1016533
            $fieldsArr = explode('.', $selectFields[0]);
            $mainColumn = end($fieldsArr);
            $ids = array_column($fetched, $mainColumn);

            if ($prefetchCondition->getMainCondition() === 'isNot') {
                $res[] = $prefetchCondition->getPrimaryEntityName() . "." . $prefetchCondition->getPrimarySearchFieldName() . " " . 'NOT IN(' . implode(',', $ids) . ')';
            } else {
                $res[] = $prefetchCondition->getPrimaryEntityName() . "." . $prefetchCondition->getPrimarySearchFieldName() . " " . ' IN(' . implode(',', $ids) . ')';
            }
        }

        return $res;
    }

    /**
     * @param \App\BasicRum\Layers\DataLayer\Query\Plan\SecondaryFilter $filter
     * @param array $limitFilters
     * @return bool
     */
    private function shouldLimitPrefetchCondition(
        \App\BasicRum\Layers\DataLayer\Query\Plan\SecondaryFilter $filter,
        array $limitFilters
    )
    {
        if (empty($limitFilters)) {
            return false;
        }

        $searchKey = $filter->getPrimaryEntityName() . '.' . $filter->getPrimarySearchFieldName();

        /** @var \App\BasicRum\Layers\DataLayer\Query\Plan\SecondaryFilter $limitFilter */
        foreach ($limitFilters as $limitFilter) {
            if (strpos($limitFilter, $searchKey) !== false) {
                return true;
            }
        }

        return false;
    }

    private function getSingleRowValue(array $data)
    {
        return array_values($data[0])[0];
    }

    private function isMinMaxQuery(array $selectFields)
    {
        if (strpos(print_r($selectFields, true),'MAX(') !== false) {
            return true;
        }


        if (strpos(print_r($selectFields, true),'MIN(') !== false) {
            return true;
        }

        return false;
    }

    /**
     * @param \App\BasicRum\Layers\DataLayer\Query\Plan\SecondaryFilter $filter
     * @return string
     */
    private function getPrefetchCacheKey(\App\BasicRum\Layers\DataLayer\Query\Plan\SecondaryFilter $filter) {
        $dbUrlArr = explode('/', getenv('DATABASE_URL'));

        return end($dbUrlArr) . 'prefetch_condition_query_data_layer_' .
            md5(print_r($filter->getPrefetchSelect()->getFields(), true)) .
            md5($filter->getPrefetchCondition()->getWhere() . print_r($filter->getPrefetchCondition()->getParams(), true));
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