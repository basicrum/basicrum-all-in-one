<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Runner;

use App\BasicRum\Cache\Storage;
use Doctrine\DBAL\Connection;

class SecondaryFilter
{
    /** @var Storage */
    private $cacheAdapter;
    /** @var Connection */
    private $connection;

    public function __construct(
        Connection $connection,
        Storage $cacheAdapter
    ) {
        $this->connection = $connection;
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function process(array $filters, array $limitFilters): array
    {
        $res = [];

        // Concept for prefetch filter
        /** @var \App\BasicRum\Layers\DataLayer\Query\Plan\SecondaryFilter $prefetchCondition */
        foreach ($filters as $prefetchCondition) {
            $whereArr = [];

            $cacheKey = $this->getPrefetchCacheKey($prefetchCondition, $limitFilters);

            $whereArr[] = $prefetchCondition->getPrefetchCondition()->getWhere();
            $params = $prefetchCondition->getPrefetchCondition()->getParams();

            $selectFields = $prefetchCondition->getPrefetchSelect()->getFields();

            if ($this->shouldLimitPrefetchCondition($prefetchCondition, $limitFilters)) {
                foreach ($limitFilters as $limitFilter) {
                    $transformed = str_replace(
                        $prefetchCondition->getPrimaryTableName(),
                        $prefetchCondition->getSecondaryTableName(),
                        $limitFilter
                    );

                    $whereArr[] = $transformed;
                }
            }

            if ($this->cacheAdapter->hasItem($cacheKey)) {
                $fetched = $this->cacheAdapter->getItem($cacheKey)->get();
            } else {
                //Playing a bit with generating low level query
                $sql = 'SELECT '.implode(',', $selectFields).' ';
                $sql .= 'FROM '.$prefetchCondition->getSecondaryTableName().' ';
                $sql .= 'WHERE '.implode(' AND ', $whereArr);

                foreach ($params as $search => $replace) {
                    $r = '\''.$replace.'\'';
                    $sql = str_replace(':'.$search, $r, $sql);
                }

                // Quickly hacking because I am still not sure about the design of DataLayer stuff
                // We are doing dirty string replace but that is fine for current concept
                if (false !== strpos($sql, 'visits_overview.page_view_id')) {
                    $sql = str_replace(
                        'visits_overview.page_view_id',
                        'visits_overview.first_page_view_id',
                        $sql
                    );

                    $sql = str_replace(
                        "visits_overview.page_views_count = '1' AND ",
                        '',
                        $sql);

                    $res[] = $prefetchCondition->getPrimaryTableName().
                        '.'.
                        $prefetchCondition->getPrimarySearchFieldName().
                        ' '.
                        ' IN('.$sql.')';
                    continue;
                }

                $fetched = $this->connection->fetchAll($sql);

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
                    $res[] = $prefetchCondition->getPrimaryTableName().'.'.$prefetchCondition->getPrimarySearchFieldName().' '.$prefetchCondition->getMainCondition().' '.$value;
                }
                continue;
            }

            //@todo: Maybe better to use custom hydrator https://stackoverflow.com/a/27823082/1016533
            $fieldsArr = explode('.', $selectFields[0]);
            $mainColumn = end($fieldsArr);
            $ids = array_column($fetched, $mainColumn);

            if ('isNot' === $prefetchCondition->getMainCondition()) {
                $res[] = $prefetchCondition->getPrimaryTableName().'.'.$prefetchCondition->getPrimarySearchFieldName().' '.'NOT IN('.implode(',', $ids).')';
            } else {
                $res[] = $prefetchCondition->getPrimaryTableName().'.'.$prefetchCondition->getPrimarySearchFieldName().' '.' IN('.implode(',', $ids).')';
            }
        }

        return $res;
    }

    /**
     * @return bool
     */
    private function shouldLimitPrefetchCondition(
        \App\BasicRum\Layers\DataLayer\Query\Plan\SecondaryFilter $filter,
        array $limitFilters
    ) {
        if (empty($limitFilters)) {
            return false;
        }

        $searchKey = $filter->getPrimaryTableName().'.'.$filter->getPrimarySearchFieldName();

        /** @var \App\BasicRum\Layers\DataLayer\Query\Plan\SecondaryFilter $limitFilter */
        foreach ($limitFilters as $limitFilter) {
            if (false !== strpos($limitFilter, $searchKey)) {
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
        if (false !== strpos(print_r($selectFields, true), 'MAX(')) {
            return true;
        }

        if (false !== strpos(print_r($selectFields, true), 'MIN(')) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    private function getPrefetchCacheKey(\App\BasicRum\Layers\DataLayer\Query\Plan\SecondaryFilter $filter, array $limitFiltes)
    {
        $dbUrlArr = explode('/', $_ENV['DATABASE_URL']);

        return end($dbUrlArr).'prefetch_condition_query_data_layer_'.
            md5(print_r($limitFiltes, true)).
            md5(print_r($filter->getPrefetchSelect()->getFields(), true)).
            md5($filter->getPrefetchCondition()->getWhere().print_r($filter->getPrefetchCondition()->getParams(), true));
    }

    public function getEntityClassName(string $className): string
    {
        return '\App\Entity\\'.$className;
    }
}
