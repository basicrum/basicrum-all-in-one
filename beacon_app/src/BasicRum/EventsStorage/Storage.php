<?php

declare(strict_types=1);

namespace App\BasicRum\EventsStorage;

use App\BasicRum\EventsStorage\FileSystem\Raw;
use App\BasicRum\EventsStorage\FileSystem\Bundle;
use App\BasicRum\EventsStorage\FileSystem\Init;

class Storage
{

    public function writeRawBeacons(array $beacons) : bool
    {
        return true;
    }

    // RAW BEACONS - OPERATIONS
    public function listRawBeaconsHosts() : array
    {
        $raw = new Raw();
        return $raw->listRawBeaconsHosts();
    }

    public function listRawBeaconsInHost(string $host) : array
    {
        $raw = new Raw();
        return $raw->listRawBeaconsInHost($host);
    }

    public function deleteRawBeacons(array $beaconsPaths) : array
    {
        $raw = new Raw();
        return $raw->deleteRawBeacons($beaconsPaths);
    }

    // BUNDLE - OPERATIONS
    public function createBeaconsBundle(string $host, array $rawBeaconsPaths) : array
    {
        $bundleStorage = new Bundle();

        $rawBeaconsArr = [];

        foreach ($rawBeaconsPaths as $path) {
            $rawBeaconsArr[] = file_get_contents($path);
        }

        return $bundleStorage->persistBundle($host, $rawBeaconsArr);
    }

    public function readBundle(string $path) : string|bool
    {
        return file_get_contents($path);
    }

    public function getBundleBeacons(string $path) : array
    {
        $bundleContent = $this->readBundle($path);
        $lines = explode("\n", $bundleContent);

        $beaconsArr = [];

        foreach ($lines as $line) {
            $beaconData = json_decode($line, true);
            // @todo: Add stats for lines that can't be parsed
            if (false !== $beaconData) {
                $beaconsArr[] = $beaconData;
            }
        }

        return $beaconsArr;
    }

    public function listBundlesInHosts() : array
    {
        $bundleStorage = new Bundle();
        return $bundleStorage->listAvailableBundlesInHosts();
    }

    public function moveImportedBundle(string $path) : bool
    {
        return true;
    }

    public function moveCorruptedBundle(string $path) : bool
    {
        return true;
    }

    public function initDirectories() : array
    {
        $init = new Init();
        return $init->initDirectories();
    }

    // Perhaps concatenate
    public function concatenatePreArchiveBundles()
    {

    }

}
