<?php
declare(strict_types=1);

namespace App\BasicRum\Beacon\Catcher\Storage\File;

class Time
{

    /**
     * @param string $path
     * @return int
     */
    public function getCreatedAtFromPath(string $path) : int
    {
        $parts = explode('_', $path);
        $endOfPath = end($parts);

        return (int) $parts = explode('-', basename($endOfPath))[1];
    }

}
