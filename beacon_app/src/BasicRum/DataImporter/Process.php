<?php

namespace App\BasicRum\DataImporter;

class Process
{

    private BeaconsExtractor $beaconExtractor;

    private Writer $writer;

    public function __construct(array $beaconExtractors, array $derivedExtractors)
    {
        $this->beaconExtractor = new BeaconsExtractor($beaconExtractors, $derivedExtractors);
        $this->writer = new Writer();
    }

    public function runImport(string $host, array $dataBundle): int
    {
        $extracted = $this->beaconExtractor->extract($dataBundle);

        return $this->writer->runImport($host, $extracted, 200);
    }
}
