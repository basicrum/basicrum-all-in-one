<?php

declare(strict_types=1);

namespace App\BasicRum\Workflows;

use App\BasicRum\Db\ClickHouse\Connection;
use App\BasicRum\EventsStorage\Storage;

use App\BasicRum\DataImporter;
use App\BasicRum\DataImporter\Writer;


class ImportBundles
{

    public static function run(Storage $storage, Connection $connection) : Monitor
    {
        $monitor = new Monitor(self::class);

        $bundleInHosts = $storage->listBundlesInHosts();

        $importer = new DataImporter(
            new Writer(
                $connection
            )
        );

        foreach ($bundleInHosts as $host => $bundlesPaths) {
            foreach ($bundlesPaths as $file) {

                $beacons = $storage->getBundleBeacons($file);

                // @todo: It will be great if we
//                if (!is_array($beacons))
//                {
//                    $monitor->addMarker("bundle_read_failed", $file, "no_description", 'no_value');
//
//                    $moveResult = $storage->moveCorruptedBundle($file);
//                    $monitor->addMarker("bundle_moved_to_corrupted_bundles", $file, "failure", (string) $moveResult);
//                    continue;
//                }

                $importer->import($host, $beacons);
                $monitor->addMarker("bundle_read_success", $file, "beacons_in_bundle", (string) count($beacons));

                $moveResult = $storage->moveImportedBundle($file);
                $monitor->addMarker("bundle_moved_to_imported_bundles", $file, "success", (string) $moveResult);
            }
        }

        return $monitor;
    }

}
