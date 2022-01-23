<?php

declare(strict_types=1);

namespace App\BasicRum\EventsStorage\FileSystem;

class Config
{

    public function checkConfig()
    {
        $rawDirPath = getenv('BASICRUM_STORAGE_RAW_DIR');
        $bundleDirPath = getenv('BASICRUM_STORAGE_BUNDLE_DIR');
        $importedBundleDirPath = getenv('BASICRUM_STORAGE_IMPORTED_BUNDLE_DIR');
        $corruptedBundleDirPath = getenv('BASICRUM_STORAGE_CORRUPTED_BUNDLE_DIR');
        $archiveDirPath = getenv('BASICRUM_STORAGE_ARCHIVE_DIR');
        $rootDirPath = getenv('BASICRUM_STORAGE_ROOT_DIR');

    }

}
