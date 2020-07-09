<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Catcher\Storage;

use App\BasicRum\Beacon\Catcher\Storage\File\Sort;
use App\BasicRum\Beacon\Catcher\Storage\File\Time;

class File
{
    /** @var string */
    private $rootStorageDirectory = '';

    /** @var string */
    const ROOT_STORAGE_DIR = 'var/beacons';

    /** @var string */
    const RELATIVE_RAW_STORAGE_DIR = 'raw';

    /** @var string */
    const RELATIVE_BUNDLES_STORAGE_DIR = 'bundles';

    /** @var string */
    const RELATIVE_ARCHIVE_STORAGE_DIR = 'archive';

    /** @var File\Time */
    private $time;

    private $sort;

    public function __construct()
    {
        $this->rootStorageDirectory = $this->getProjectPath().'/'.self::ROOT_STORAGE_DIR;
        $this->time = new Time();
        $this->sort = new Sort();
    }

    public function storeBeacon(string $beacon): void
    {
        file_put_contents($this->generateFileName($beacon), $beacon);
    }

    private function generateFileName(string $beacon): string
    {
        // @todo: Check if this could lead to vulnerability because we get user input and we use this input in order to write on FS
        $origin = !empty($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'unknown';
        $parsed = parse_url($origin);
        $suffix = str_replace('.', '_', $parsed['host']);

        return $this->getRawBeaconsDir().'/'.$suffix.'_'.md5($beacon).'-'.time().'-'.rand(1, 99999).'.json';
    }

    public function generateBundleFromRawBeacons(): int
    {
        $beaconFiles = glob($this->getRawBeaconsDir().'/*.json');

        $entries = [];

        foreach ($beaconFiles as $filePath) {
            $entries[] = [
                'id' => basename($filePath),
                'beacon_data' => file_get_contents($filePath),
            ];
        }

        $this->deleteRawBeacons($beaconFiles);

        $name = time().'.json';

        $this->persistBundle($name, json_encode($entries));

        return \count($entries);
    }

    private function getProjectPath(): string
    {
        return explode('/src/BasicRum', __DIR__)[0];
    }

    private function getRawBeaconsDir(): string
    {
        return $this->rootStorageDirectory.'/'.self::RELATIVE_RAW_STORAGE_DIR;
    }

    /**
     * @return string
     */
    private function getArchiveDir()
    {
        return $this->rootStorageDirectory.'/'.self::RELATIVE_ARCHIVE_STORAGE_DIR;
    }

    /**
     * @return string
     */
    private function getBundlesDir()
    {
        return $this->rootStorageDirectory.'/'.self::RELATIVE_BUNDLES_STORAGE_DIR;
    }

    public function archiveBundles(): int
    {
        $count = 0;

        foreach ($this->getBundleFilePaths() as $filePath) {
            $baseName = basename($filePath);

            if (class_exists('\ZipArchive')) {
                $zip = new \ZipArchive();

                $zipFileName = $baseName.'.zip';

                if (true === $zip->open($this->getArchiveDir().'/'.$zipFileName, \ZipArchive::CREATE)) {
                    $zip->addFile($filePath, basename($filePath));
                    $zip->close();
                    unlink($filePath);
                }
            } else {
                rename($this->getBundlesDir().'/'.$baseName, $this->getArchiveDir().'/'.$baseName);
            }

            ++$count;
        }

        return $count;
    }

    public function persistBundle(string $name, string $content): void
    {
        $path = $this->getBundlesDir().'/'.$name;
        file_put_contents($path, $content);
    }

    public function getBundleFilePaths(): array
    {
        return glob($this->getBundlesDir().'/*.json');
    }

    public function initFolders(): array
    {
        $folders = [
            $this->rootStorageDirectory,
            $this->getRawBeaconsDir(),
            $this->getArchiveDir(),
            $this->getBundlesDir(),
        ];

        $res = [];

        foreach ($folders as $folder) {
            try {
                $res[$folder] = mkdir($folder, 0777, true);
                echo 'Directory created: '.$folder.PHP_EOL;
            } catch (\Exception $e) {
                echo 'Directory already exist: '.$folder.PHP_EOL;
            }
        }

        return $res;
    }

    public function deleteRawBeacons(array $rawBeaconFilesArray): void
    {
        foreach ($rawBeaconFilesArray as $rawBeaconFileName) {
            unlink($rawBeaconFileName);
        }
    }
}
