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

    /** @var File\Time */
    private $time;

    private $sort;

    public function __construct()
    {
        $this->rootStorageDirectory = $this->getProjectPath() . '/' . self::ROOT_STORAGE_DIR;
        $this->time = new File\Time();
        $this->sort = new File\Sort();
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

        return $this->getRawBeaconsDir() . '/' . $suffix . '_' . md5($beacon) . '-' . time() . '-' . rand(1, 99999) . '.json';
    }

    /**
     * @return array
     */
    public function fetchBeacons()
    {
        $beaconFiles = glob($this->getRawBeaconsDir() . '/*.json');

        $data = [];

        foreach ($beaconFiles as $filePath) {
            $data[] = [
                0 => $this->time->getCreatedAtFromPath($filePath),
                1 => file_get_contents($filePath)
            ];
        }

        $this->sort->sortBeacons($data);

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

        $zipFileName = time() . '.zip';

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