<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Reader;

use App\BasicRum\Beacon\Catcher\Storage\File;

class MonolithCatcher
{

    /** @var File */
    private $storage;

    public function __construct()
    {
        $this->storage = new File();
    }

    /**
     * @return mixed
     */
    public function read()
    {
        return $this->storage->fetchBeacons();
    }

}

