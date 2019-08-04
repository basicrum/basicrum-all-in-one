<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Reader;

use App\BasicRum\Beacon\Catcher\Storage\File\Time;
use App\BasicRum\Beacon\Catcher\Storage\File\Sort;

class CatcherService
{

    /** @var string */
    private $bundleFile = '';

    /** @var Time */
    private $time;

    /** @var Sort */
    private $sort;

    /**
     * MonolithCatcher constructor.
     * @param string $bundleFile
     */
    public function __construct(string  $bundleFile)
    {
        $this->time = new Time();
        $this->sort = new Sort();
        $this->bundleFile = $bundleFile;
    }

    /**
     * @return mixed
     */
    public function read()
    {
        $content = file_get_contents($this->bundleFile);

        $beacons = json_decode($content, true);

        $data = [];

        foreach ($beacons as $bundleEntry) {
            $beaconData = json_decode($bundleEntry['beacon_data'], true);
            $beaconData['created_at'] = $bundleEntry['created_at'];

            //var_dump($beaconData['created_at']);

            $data[] = [
                0 => $this->time->getCreatedAtFromPath($bundleEntry['id']),
                1 => json_encode($beaconData)
            ];
        }

        $this->sort->sortBeacons($data);

        return $data;
    }

}

