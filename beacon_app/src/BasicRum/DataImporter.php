<?php

declare(strict_types=1);

namespace App\BasicRum;

use App\BasicRum\Metrics\ImportCollaborator;

use App\BasicRum\DataImporter\BeaconsExtractor;
use App\BasicRum\DataImporter\Writer;
use App\BasicRum\Workflows\Monitor;

class DataImporter
{

    /** @var Writer */
    private Writer $writer;

    private int $batchSize = 200;

    public function __construct(Writer $writer)
    {
        $this->writer = $writer;
    }

    public function import(string $host, array $data) : Monitor
    {
        $monitor = new Monitor(self::class);

        // Experimental code
        $refined = [];

        foreach ($data as $key => $beacon) {

            $bData = $beacon;

            // @todo: Cover this case on in a specific unit test.
            // could be null or false
            if (!$bData) {
                $monitor->addMarker("beacon_line_decode_failed", (string) $key, "beacon_line", (string) $beacon);
                continue;
            }

            // @todo: for now we just skip quit beacons but later we should import quit beacons and do analyzes
            if (isset($bData["rt_quit"])) {
                $monitor->addMarker("beacon_quit_skipped", (string) $key, "beacon_line", (string) $beacon);
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

        $count = $this->writer->runImport($host, $extracted, $this->batchSize);

        $monitor->addMarker("import_beacons_in_db_for_host", $host, "beacon_line", (string) $count);

        return $monitor;
    }

}
