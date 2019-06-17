<?php
declare(strict_types=1);

namespace App\BasicRum\Beacon\Catcher\Storage;

require_once __DIR__ . '/StorageInterface.php';

use \ZipArchive;

class File
    implements StorageInterface
{

    /** @var string */
    private $rootStorageDirectory             = '';

    /** @var string */
    CONST ROOT_STORAGE_DIR                    = 'var/beacons';

    /** @var string */
    CONST RELATIVE_RAW_STORAGE_DIR            = 'raw';

    /** @var string */
    CONST RELATIVE_BEFORE_ARCHIVE_STORAGE_DIR = 'before_archive_raw';

    /** @var string */
    CONST RELATIVE_ARCHIVE_STORAGE_DIR        = 'archive';

    public function __construct()
    {
        $this->rootStorageDirectory = $this->getProjectPath() . '/' . self::ROOT_STORAGE_DIR;
    }

    /**
     * @param string $beacon
     */
    public function storeBeacon(string $beacon) : void
    {
        file_put_contents($this->generateFileName($beacon), $beacon);
    }

    /**
     * @param string $beacon
     * @return string
     */
    private function generateFileName(string $beacon) : string
    {
        // @todo: Check if this could lead to vulnerability because we get user input and we use this input in order to write on FS
        $origin = !empty($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'unknown';
        $parsed = parse_url($origin);
        $suffix = str_replace('.', '_', $parsed['host']);

        return $this->getRawBeaconsDir() . '/' . $suffix . '_' . md5($beacon) . '-' . mktime() . '-' . rand(1, 99999) . '.json';
    }

    /**
     * @return array
     */
    public function fetchBeacons()
    {
        $this->archiveBeacons();

        $beaconFiles = glob($this->getRawBeaconsDir() . '/*.json');

        $data = [];

        foreach ($beaconFiles as $filePath) {
            $parts = explode('-', $filePath);

            $data[] = [
                0 => array_slice($parts, -2, 1)[0],
                1 => file_get_contents($filePath)
            ];
        }

        // Sort the array
        usort($data, function($element1, $element2) {
            return $element1[0] - $element2[0];
        });

        $this->moveBeacons($beaconFiles);

        return $data;
    }

    /**
     * @param array $beaconFiles
     */
    private function moveBeacons(array $beaconFiles) : void
    {
        foreach ($beaconFiles as $filePath) {
            $newPath = str_replace(self::RELATIVE_RAW_STORAGE_DIR, self::RELATIVE_BEFORE_ARCHIVE_STORAGE_DIR, $filePath);
            rename($filePath, $newPath);
        }
    }

    /**
     * @return string
     */
    private function getProjectPath() : string
    {
        return explode('/src/BasicRum',__DIR__)[0];
    }

    /**
     * @return string
     */
    private function getBeforeArchiveTemporaryDir() : string
    {
        return $this->rootStorageDirectory . '/' . self::RELATIVE_BEFORE_ARCHIVE_STORAGE_DIR;
    }

    /**
     * @return string
     */
    private function getRawBeaconsDir() : string
    {
        return $this->rootStorageDirectory . '/' . self::RELATIVE_RAW_STORAGE_DIR;
    }

    /**
     * @return string
     */
    private function getArchiveDir()
    {
        return $this->rootStorageDirectory . '/' . self::RELATIVE_ARCHIVE_STORAGE_DIR;
    }


    public function archiveBeacons() : void
    {
        $zip = new ZipArchive;

        $zipFileName = mktime() . '.zip';

        if ($zip->open($this->getArchiveDir() . '/' . $zipFileName, ZipArchive::CREATE) === TRUE)
        {
            $beaconFiles = glob($this->getBeforeArchiveTemporaryDir() . '/*.json');

            foreach ($beaconFiles as $filePath) {
                $zip->addFile($filePath, basename($filePath));
            }

            $zip->close();

            foreach ($beaconFiles as $filePath) {
                unlink($filePath);
            }
        }
    }

    public function initFolders() : void
    {
        mkdir($this->rootStorageDirectory);
        mkdir($this->getRawBeaconsDir());
        mkdir($this->getBeforeArchiveTemporaryDir());
        mkdir($this->getArchiveDir());
    }

}