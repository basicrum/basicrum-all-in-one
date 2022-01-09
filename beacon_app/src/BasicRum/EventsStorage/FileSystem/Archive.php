<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Catcher\Storage;

class Archive
{

    /** @var Base */
    private Base $base;

    public function __construct()
    {
        $this->base = new Base();
    }

    public function archiveBundles($host, $filePath) : string
    {
        $baseName = basename($filePath);

        $archiveHostDir = $this->base->getArchiveHostDir($host);

        $generatedName = "";

        if (!is_dir($archiveHostDir)) {
            mkdir($archiveHostDir, 0777);
        }

        if (class_exists('\ZipArchive')) {
            $zip = new \ZipArchive();

            $zipFileName = $baseName.'.zip';

            $generatedName = $archiveHostDir.'/'.$zipFileName;

            if (true === $zip->open($archiveHostDir.'/'.$zipFileName, \ZipArchive::CREATE)) {
                $zip->addFile($filePath, basename($filePath));
                $zip->close();
            }
        } else {
            $generatedName = $archiveHostDir.'/'.$baseName;
            rename($filePath, $archiveHostDir.'/'.$baseName);
        }

        return $generatedName;
    }

}
