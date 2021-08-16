<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Catcher\Storage;

class Bundle
{

    /** @var Base */
    private $base;

    public function __construct()
    {
        $this->base = new Base();
    }

    public function generateBundleFromRawBeacons(): int
    {
        $rawBeaconsHostsDirs = array_diff(
            scandir(
                $this->base->getRootRawBeaconsDir()
            ),
            ['..', '.'] // exclude "." and ".."
        );

        $entriesCount = 0;

        foreach ($rawBeaconsHostsDirs as $dir) {
            $beaconFiles = glob($this->base->getRawBeaconsHostDir($dir).'/*.json');

            $entries = [];
    
            foreach ($beaconFiles as $filePath) {
                $entriesCount++;

                $entries[] = [
                    'id' => basename($filePath),
                    'beacon_data' => file_get_contents($filePath),
                ];
            }
    
            $this->persistBundle($dir, json_encode($entries));

            foreach ($beaconFiles as $filePath) {
                unlink($filePath);
            }
        }

        return $entriesCount;
    }

    public function persistBundle(string $directory, string $content): void
    {
        $absoluteDirPath = $this->base->getBundlesHostDir($directory);

        if (!is_dir($absoluteDirPath)) {
            mkdir($absoluteDirPath);
        }

        $name = time().'.json';
        $path = $absoluteDirPath.'/'.$name;

        file_put_contents($path, $content);
    }

    public function listAvailableBundlesInHosts()
    {
        $bundlesHostsDirs = array_diff(
            scandir(
                $this->base->getRootBundlesDir()
            ),
            ['..', '.'] // exclude "." and ".."
        );

        $bundlesInHosts = [];

        foreach ($bundlesHostsDirs as $dir) {
            $bundlesInHosts[$dir] = glob($this->base->getBundlesHostDir($dir).'/*.json');  
        }

        return $bundlesInHosts;
    }

}