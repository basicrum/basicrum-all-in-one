<?php

declare(strict_types=1);

namespace App\BasicRum\EventsStorage\FileSystem;

class Raw
{

    /** @var Base */
    private Base $base;

    /** @var string */
    const UNKNOWN_HOST_PLACEHOLDER = 'unknown';

    public function __construct()
    {
        $this->base = new Base();
    }

    public function storeBeacon(string $beacon): void
    {
        $filename = $this->generateFileName($beacon);
        file_put_contents($this->generateFileName($filename), $beacon);
        chmod($filename, 0777);
    }

    public function deleteRawBeacons(array $beaconsPaths) : array
    {
        $res = [];

        foreach ($beaconsPaths as $path) {
            $res[$path] = unlink($path);
        }

        return $res;
    }

    public function listRawBeaconsHosts() : array
    {
        return array_diff(
            scandir(
                $this->base->getRootRawBeaconsDir()
            ),
            ['..', '.'] // exclude "." and ".."
        );
    }

    public function listRawBeaconsInHost(string $hostDir) : array
    {
        return glob($this->base->getRawBeaconsHostDir($hostDir).'/*.json');
    }

    /**
     * @param string $beacon
     * @return string
     */
    private function generateFileName(string $beacon) : string
    {
        $origin = !empty($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : self::UNKNOWN_HOST_PLACEHOLDER;

        $hostNormalized = self::UNKNOWN_HOST_PLACEHOLDER;

        if (self::UNKNOWN_HOST_PLACEHOLDER !== $origin) {
            $parsed = parse_url($origin);

            $hostNormalized = $this->hostToPath($parsed['host']);
        }

        $storagePath = $this->base->getRawBeaconsHostDir($hostNormalized);

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0777);
        }

        return $storagePath . '/' . $hostNormalized . '_' . md5($beacon) . '-' . time() . '-' . rand(1, 99999) . '.json';
    }

    /**
     * @param string $host
     * @return string
     */
    private function hostToPath(string $host) : string
    {
        if (self::UNKNOWN_HOST_PLACEHOLDER === $host) {
            return self::UNKNOWN_HOST_PLACEHOLDER;
        }

        $host = str_replace('-', '_', $host);

        return str_replace('.', '_', $host);
    }

}
