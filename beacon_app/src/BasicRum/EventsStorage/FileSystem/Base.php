<?php

declare(strict_types=1);

namespace App\BasicRum\EventsStorage\FileSystem;

class Base
{

    /** @var string */
    private string $rootStorageDirectory = '';

    /** @var string */
    const ROOT_STORAGE_DIR = 'var/beacons_tmp';

    /** @var string */
    const RELATIVE_RAW_STORAGE_DIR = 'raw';

    /** @var string */
    const RELATIVE_IMPORTED_BUNDLES_STORAGE_DIR = 'imported_bundles';

    /** @var string */
    const RELATIVE_CORRUPTED_BUNDLES_STORAGE_DIR = 'corrupted_bundles';

    /** @var string */
    const RELATIVE_BUNDLES_STORAGE_DIR = 'bundles';

    /** @var string */
    const RELATIVE_ARCHIVE_STORAGE_DIR = 'archive';

    public function __construct()
    {
        $this->rootStorageDirectory = $this->getProjectPath().'/'.self::ROOT_STORAGE_DIR;
    }

    public function getRawBeaconsHostDir(string $host): string
    {
        return $this->rootStorageDirectory.'/'.self::RELATIVE_RAW_STORAGE_DIR.'/'.$host;
    }

    /**
     * @return string
     */
    public function getArchiveHostDir(string $host): string
    {
        return $this->rootStorageDirectory.'/'.self::RELATIVE_ARCHIVE_STORAGE_DIR.'/'.$host;
    }

    /**
     * @return string
     */
    public function getBundlesHostDir(string $host): string
    {
        return $this->rootStorageDirectory.'/'.self::RELATIVE_BUNDLES_STORAGE_DIR.'/'.$host;
    }

    /**
     * @return string
     */
    public function getRootStorageDir(): string
    {
        return $this->rootStorageDirectory;
    }

    public function getRootRawBeaconsDir(): string
    {
        return $this->rootStorageDirectory.'/'.self::RELATIVE_RAW_STORAGE_DIR;
    }

    /**
     * @return string
     */
    public function getRootArchiveDir(): string
    {
        return $this->rootStorageDirectory.'/'.self::RELATIVE_ARCHIVE_STORAGE_DIR;
    }

    /**
     * @return string
     */
    public function getRootBundlesDir(): string
    {
        return $this->rootStorageDirectory.'/'.self::RELATIVE_BUNDLES_STORAGE_DIR;
    }

    /**
     * @return string
     */
    private function getProjectPath(): string
    {
        return explode('/src/BasicRum', __DIR__)[0];
    }

}
