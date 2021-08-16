<?php

declare(strict_types=1);

namespace App\BasicRum;

use App\BasicRum\Metrics\ImportCollaborator;
use App\BasicRum\DataImporter\Process;

class DataImporter
{

    private Process $importProcess;

    public function __construct()
    {
        $importCollaborator = new ImportCollaborator();

        $this->importProcess = new Process(
            $importCollaborator->getBeaconExtractors(),
            $importCollaborator->getDerivedExtractors()
        );
    }

    public function import(string $host, array $data)
    {
        // Experimental code
        $refined = [];

        foreach ($data as $key => $beacon) {
            $refined[$key] = json_decode($beacon["beacon_data"], true);

            //fix date;
            // @todo: get the right date when persisting beacons and remove this hack!
            $date = $refined[$key]["created_at"];

            if (18 === strlen($date)) {
                $parts = explode(" ", $date);
                $date = $parts[0] . " 0" . $parts[1];
            }

            $refined[$key]["created_at"] = $date;
        }

        $importedCnt = $this->importProcess->runImport($host, $refined);

        return $importedCnt;
    }

}
