<?php

declare(strict_types=1);

namespace App\BasicRum\EventsStorage\FileSystem;

class Bundle
{

    /** @var Base */
    private Base $base;

    /** @var string */
    const BUNDLE_FILE_EXTENSION = 'beacnbundl';

    public function __construct()
    {
        $this->base = new Base();
    }

    public function persistBundle(string $host, array $rawBeaconsArr) : array
    {
        $absoluteDirPath = $this->base->getBundlesHostDir($host);

        if (!is_dir($absoluteDirPath)) {
            mkdir($absoluteDirPath, 0777);
        }

        $bundleName = time().'.'.self::BUNDLE_FILE_EXTENSION;
        $finalPath = $absoluteDirPath.'/'.$bundleName;

        file_put_contents($finalPath, implode("\n", $rawBeaconsArr));
        chmod($finalPath, 0777);

        return [
            'size' => filesize($finalPath)
        ];
    }

    public function listAvailableBundlesInHosts() : array
    {
        $bundlesHostsDirs = array_diff(
            scandir(
                $this->base->getRootBundlesDir()
            ),
            ['..', '.'] // exclude "." and ".."
        );

        $bundlesInHosts = [];

        foreach ($bundlesHostsDirs as $dir) {
            $bundlesInHosts[$dir] = glob($this->base->getBundlesHostDir($dir).'/*.'.self::BUNDLE_FILE_EXTENSION);
        }

        return $bundlesInHosts;
    }

}
