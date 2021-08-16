<?php

declare(strict_types=1);

namespace App\BasicRum\DataImporter;

use WhichBrowser\Parser;

class BeaconsExtractor
{

    private array $beaconExtractors;

    private array $derivedExtractors;

    public function __construct(
        array $beaconExtractors,
        array $derivedExtractors
    )
    {
        $this->beaconExtractors = $beaconExtractors;
        $this->derivedExtractors = $derivedExtractors;
    }

    public function extract(array $beacons): array
    {
        $extracted = [];

        foreach ($beacons as $beacon) {
            $line = [];

            foreach ($this->beaconExtractors as $fieldKey => $extractor) {
                $line[$fieldKey] = call_user_func($extractor, $beacon);
            }

            foreach ($this->derivedExtractors as $deriveFromMetric => $extractors) {
                // Experimenting with derived metrics
                $result = new Parser($line[$deriveFromMetric]);

                $data = $result->toArray();

                foreach ($extractors as $fieldKey => $extractor) {
                    $line[$fieldKey] = call_user_func($extractor, $data);
                }
            }

            $extracted[] = $line;
        }

        return $extracted;
    }

}
