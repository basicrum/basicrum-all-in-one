<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Catcher\Storage;

class Archive
{

    public function __construct()
    {
        
    }

    public function archiveBundles() : int
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

}