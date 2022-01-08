<?php

declare(strict_types=1);

namespace App\BasicRum\Db\ClickHouse;

use ClickHouseDB\Client;

class Connection
{

    /** @var Client */
    private Client $client;

    public function __construct(array $config)
    {
        $this->client = new Client($config);
    }

    public function isConnected() : bool
    {
        return $this->client->ping();
    }

    public function selectRows(string $sql, array $bindings = []) : array
    {
        return $this->client->select($sql, $bindings)->rows();
    }

    public function write(string $sql, array $bindings = [])
    {
        $this->client->write($sql, $bindings);
    }

    public function showTables() : array
    {
        return $this->client->showTables();
    }

    public function insert(string $table, array $values, array $columns)
    {
        $this->client->insert($table, $values, $columns);
    }
}
