<?php

declare(strict_types=1);

namespace App\BasicRum\ResourceTiming;

class Decompressor
{

    const RESOURCE_DELIMITER = ';';
    const TIMINGS_DELIMITER  = ',';

    /**
     * @param string $timings
     * @return array
     */
    public function decompress(string $timings) : array
    {
        $decompressedResources = [];

        $resources = explode(self::RESOURCE_DELIMITER, $timings);

        $startTime = 0;

        foreach ($resources as $timings) {
            $data = explode(self::TIMINGS_DELIMITER, $timings);

            $timingsData = $this->_extractTimings($data, $startTime);

            $startTime = $timingsData['start'];

            $decompressedResources[] = [
                'url_id'    => $data[0],
                'start'     => $timingsData['start'],
                'duration'  => $timingsData['duration']
            ];
        }

        return $decompressedResources;
    }

    /**
     * @param array $data
     * @param int $startTime
     * @return array
     */
    private function _extractTimings(array $data, int $startTime) : array
    {
        $startOffset  = 0;
        $duration     = 0;

        if (isset($data[1])) {
            $startOffset = base_convert($data[1], 36, 10);
        }

        if (isset($data[2])) {
            $duration = base_convert($data[2], 36, 10);
        }

        return [
            'start'    => $startOffset + $startTime,
            'duration' => $duration
        ];
    }

}