<?php

declare(strict_types=1);

namespace App\BasicRum\Workflows;

use App\BasicRum\EventsStorage\Storage;

class BundleRawBeacons
{

    public static function run(Storage $storage) : Monitor
    {
        $monitor = new Monitor(self::class);

        $rawBeaconHosts = $storage->listRawBeaconsHosts();

        foreach ($rawBeaconHosts as $host) {
            $rawBeaconsPaths = $storage->listRawBeaconsInHost($host);

            $createdBundlesRes = $storage->createBeaconsBundle($host, $rawBeaconsPaths);

            $deletedRawBeacons = $storage->deleteRawBeacons($rawBeaconsPaths);
        }

        return $monitor;
    }

}
