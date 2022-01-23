<?php

declare(strict_types=1);

namespace App\BasicRum\EventsStorage;

use App\BasicRum\EventsStorage\FileSystem\Bundle;
use App\BasicRum\EventsStorage\FileSystem\Init;

class Storage
{

    public function writeRawBeacons(array $beacons) : bool
    {
        return true;
    }

    public function createBeaconsBundle() : bool
    {
        return true;
    }

    public function readBundle(string $path) : string|bool
    {
        return file_get_contents($path);
    }

    public function getBundleBeacons(string $path) : mixed
    {
        return json_decode($this->readBundle($path), true);
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
