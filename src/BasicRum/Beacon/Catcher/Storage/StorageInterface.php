<?php

namespace App\BasicRum\Beacon\Catcher\Storage;

interface StorageInterface
{

    /**
     * @param string $beacon
     */
    public function storeBeacon($beacon);


    /**
    * @return array
    */
    public function fetchBeacons();

}