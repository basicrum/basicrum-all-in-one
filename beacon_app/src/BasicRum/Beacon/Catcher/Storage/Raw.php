<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Catcher\Storage;

class Raw
{

    /** @var Base */
    private $base;

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

    /**
     * @param string $beacon
     * @return string
     */
    private function generateFileName($beacon) : string
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

        return str_replace('.', '_', $host);
    }

}
