<?php
declare(strict_types=1);

namespace App\BasicRum\Beacon\Catcher\Storage;

require_once __DIR__ . '/StorageInterface.php';

class File
    implements StorageInterface
{

    /** @var string */
    private $storageDirectory = '';

    public function __construct()
    {
        $projectPath = explode('/src/BasicRum',__DIR__)[0];
        $storageDir  = 'var/beacons/raw';
        $this->storageDirectory = $projectPath . '/' . $storageDir;
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
        return [];
    }

}