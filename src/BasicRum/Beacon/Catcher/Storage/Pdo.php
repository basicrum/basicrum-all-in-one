<?php

namespace App\BasicRum\Beacon\Catcher\Storage;

require_once __DIR__ . '/StorageInterface.php';

class Pdo
    implements StorageInterface
{

    private $pdo;

    public function __construct()
    {
        //@todo Make this to be read from config or ENV variables
        $dsn = 'mysql:host=db;dbname=basicrum_demo';
        $username = 'roottest';
        $password = 'roottest';
        $options = array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );

        $this->pdo = new \PDO($dsn, $username, $password, $options);
    }

    /**
     * @param string $beacon
     */
    public function storeBeacon($beacon)
    {
        $sql = "INSERT INTO beacons (beacon_data, created_at) VALUES (?, CURRENT_TIMESTAMP)";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute([$beacon]);

        //@todo: We need to send output information in case of errors
    }

    /**
     * @return array
     */
    public function fetchBeacons()
    {
        return [];
    }

}