<?php
declare(strict_types=1);

namespace App\BasicRum\Beacon\Catcher\Storage;

require_once __DIR__ . '/StorageInterface.php';

class File
    implements StorageInterface
{

    /** @var string */
    private $storageDirectory = '';

    CONST RAW_STORAGE_DIR            = 'var/beacons/raw';

    CONST BEFORE_ARCHIVE_STORAGE_DIR = 'var/beacons/before_archive_raw';

    public function __construct()
    {
        $projectPath = explode('/src/BasicRum',__DIR__)[0];
        $this->storageDirectory = $projectPath . '/' . self::RAW_STORAGE_DIR;
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
        $origin = !empty($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'unknown';
        $parsed = parse_url($origin);
        $suffix = str_replace('.', '_', $parsed['host']);

        return $this->storageDirectory . '/' . $suffix . '_' . md5($beacon) . '-' . mktime() . '-' . rand(1, 99999) . '.json';
    }

    /**
     * @return array
     */
    public function fetchBeacons()
    {
        $beaconFiles = glob($this->storageDirectory . '/*.json');

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
            $newPath = str_replace(self::RAW_STORAGE_DIR, self::BEFORE_ARCHIVE_STORAGE_DIR, $filePath);
            rename($filePath, $newPath);
        }
    }

}