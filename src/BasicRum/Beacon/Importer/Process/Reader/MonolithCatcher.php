<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Reader;

class MonolithCatcher
{

    /** @var string */
    private $filePath = '';

    /**
     * @param $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return mixed
     */
    public function read()
    {
        $beaconsJson = file_get_contents($this->filePath);

        $beacons = json_decode($beaconsJson, true);

        if (empty($beacons)) {
            return [];
        }

        foreach ($beacons as $beacon) {
            $data[] = [
                0 => $beacon['created_at'],
                1 => $beacon['beacon_data']
            ];
        }

        // Sort the array
        usort($data, function($element1, $element2) {
            $datetime1 = strtotime($element1[0]);
            $datetime2 = strtotime($element2[0]);
            return $datetime1 - $datetime2;
        });

        return $data;
    }

}

