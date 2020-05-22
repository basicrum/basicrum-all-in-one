<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Runner;

use App\BasicRum\Cache\Storage;
use Doctrine\DBAL\Connection;

class ComplexSelect
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

    public function process(\App\BasicRum\Layers\DataLayer\Query\Plan\ComplexSelect $complexSelect, array $filters): array
    {
        $whereArr = [];

        $selects = [];

        foreach ($complexSelect->getSecondarySelectDataFieldNames() as $field) {
            $selects[] = $complexSelect->getSecondarySelectTableName().'.'.$field;
        }

        foreach ($filters as $filter) {
            $corrected = str_replace(
                $complexSelect->getPrimarySelectTableName().'.'.$complexSelect->getPrimaryKeyFieldName(),
                $complexSelect->getSecondarySelectTableName().'.'.$complexSelect->getSecondaryKeyFieldName(),
                $filter
            );

            $whereArr[] = $corrected;
        }

        //Playing a bit with generating low level query

        $sql = 'SELECT '.implode(',', $selects).' ';
        $sql .= 'FROM '.$complexSelect->getSecondarySelectTableName().' ';
        $sql .= 'WHERE '.implode(' AND ', $whereArr);

        $res = $this->connection->fetchAll($sql);

        $data = [];

        // We need $complexSelect->getSecondaryKeyFieldName() as a key in new array because we will use it later for IN clause
        foreach ($res as $row) {
            $data[$row[$complexSelect->getSecondaryKeyFieldName()]] = $row;
        }

        return $data;
    }
}
