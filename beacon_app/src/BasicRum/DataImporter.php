<?php

declare(strict_types=1);

namespace App\BasicRum;

use App\BasicRum\Metrics\ImportCollaborator;

use App\BasicRum\DataImporter\BeaconsExtractor;
use App\BasicRum\DataImporter\Writer;

class DataImporter
{

    private Writer $writer;

    public function __construct()
    {
        $this->writer = new Writer();
    }

    public function import(string $host, array $data): int
    {
        // Experimental code
        $refined = [];

        foreach ($data as $key => $beacon) {
            if (empty($beacon["beacon_data"]))
            {
                continue;
            }

            $bData = json_decode($beacon["beacon_data"], true);

            // @todo: for now we just skip quit beacons but later we should import quit beacons and do analyzes
            if (isset($bData["rt_quit"])) {
                continue;
            }

            $refined[$key] = $bData;

            //fix date;
            // @todo: get the right date when persisting beacons and remove this hack!
            $date = $refined[$key]["created_at"];

            if (18 === strlen($date)) {
                $parts = explode(" ", $date);
                $date = $parts[0] . " 0" . $parts[1];
            }

            $refined[$key]["created_at"] = $date;
        }

        $importCollaborator = new ImportCollaborator();

        $beaconExtractor = new BeaconsExtractor(
            $importCollaborator->getBeaconExtractors(),
            $importCollaborator->getDerivedExtractors()
        );

        $extracted = $beaconExtractor->extract($refined);

        return $this->writer->runImport($host, $extracted, 200);
    }

}
