<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Catcher\Storage;

class Init
{

    /** @var Base */
    private Base $base;

    public function __construct()
    {
        $this->base = new Base();
    }

    public function initDirectories(): array
    {
        $directories = [
            $this->base->getRootStorageDir(),
            $this->base->getRootRawBeaconsDir(),
            $this->base->getRootBundlesDir(),
            $this->base->getRootArchiveDir()
        ];

        $res = [];

        foreach ($directories as $dir) {
            try {
                $res[$dir] = mkdir($dir, 0777, true);
                echo 'Directory created: '.$dir.PHP_EOL;
            } catch (\Exception $e) {
                echo 'Directory already exist: '.$dir.PHP_EOL;
            }
        }

        return $res;
    }

}
