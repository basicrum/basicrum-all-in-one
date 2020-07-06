<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process;

use App\BasicRum\ResourceTimingDecompressor_v_0_3_4;

class Beacon
{
    private $rumDataFlatNormalizer;
    private $resourceTimingsNormalizer;

    /** @var array */
    private $pageViewUniqueKeys = [];

    public function __construct()
    {
        $this->rumDataFlatNormalizer = new Beacon\RumDataFlatNormalizer();
        $this->resourceTimingsNormalizer = new Beacon\ResourceTimingsNormalizer();
        $this->rtNormalizer = new Beacon\RtNormalizer();
    }

    /**
     * @return array
     */
    public function extract(array $beacons)
    {
        $data = [];
        $decompressor = new ResourceTimingDecompressor_v_0_3_4();

        foreach ($beacons as $key => $beacon) {
            if (false === $beacon
                || false === strpos($beacon[1], 'user_agent')
                || false !== strpos($beacon[1], '"user_agent":""')
            ) {
                continue;
            }

            $beacons[$key] = json_decode($beacon[1], true);

            // decompress restimings
            $beacons[$key] = $this->decompressResources($beacons[$key]);

            // Legacy when we didn't have created_at in beacon data
            if (!isset($beacons[$key]['created_at'])) {
                $beacons[$key]['created_at'] = date('Y-m-d H:i:s', $beacon[0]);
            }

            $date = $beacons[$key]['created_at'];

            $pageViewKey = $this->_getPageViewKey($beacons[$key]);

            if (false === $pageViewKey) {
                unset($beacons[$key]);
                continue;
            }

            // We do not mark as page view beacons send when visitor leaves page
            // commented it out because according to latest decision we have to insert rt_quit beacon into DB as page view
            if (isset($this->pageViewUniqueKeys[$pageViewKey])) {
//                    $this->pageViewUniqueKeys[$pageViewKey] = array_merge($this->pageViewUniqueKeys[$pageViewKey], ['end' => $date]);
//                    continue;
            }

            $this->pageViewUniqueKeys[$pageViewKey] = ['start' => $date];

            // Here compile data for rum_data_flat
            $data[$key] = array_merge(
                $this->rumDataFlatNormalizer->normalize($beacons[$key]),
                $this->resourceTimingsNormalizer->normalize($beacons[$key]),
                $this->rtNormalizer->normalize($beacons[$key]),
            );

            // Here for beacons table. Need To determine AutoXHR beacons and store them only into beacons table
            $data[$key]['beacon_string'] = $beacon[1];
        }

        return $data;
    }

    // Draft in case we do not need to store AutoXHR beacons in rum_data_flat but still need to store in beacons table
    public function checkIfBeaconIsXHR(array $beacon): bool
    {
        if (isset($beacon['http_initiator']) && 'xhr' == $beacon['http_initiator']) {
            return true;
        }

        return false;
    }

    public function decompressResources(array $beacon): array
    {
        $decompressor = new ResourceTimingDecompressor_v_0_3_4();

        if (isset($beacon['restiming']) && $beacon['restiming'] && \is_string($beacon['restiming'])) {
            $resourceTimingsData = $decompressor->decompressResources(json_decode($beacon['restiming'], true));

            // replace encoded restiming with decoded
            $beacon['restiming'] = $resourceTimingsData;
        }

        return $beacon;
    }

    /**
     * @return array
     */
    public function extractPageVisitDurations(array $beacons)
    {
        foreach ($beacons as $key => $beacon) {
            if (false === $beacon) {
                continue;
            }

            $date = trim($beacon[0], "'");

            $beacons[$key] = json_decode(trim(ltrim($beacon[1], "'"), "'\n"), true);
            $beacons[$key]['date'] = $date;

            $pageViewKey = $this->_getPageViewKey($beacons[$key]);

            // We do not mark as page view beacons send when visitor leaves page
            if (isset($this->pageViewUniqueKeys[$pageViewKey])) {
                $this->pageViewUniqueKeys[$pageViewKey] = array_merge($this->pageViewUniqueKeys[$pageViewKey], ['end' => $date]);
                continue;
            }

            $this->pageViewUniqueKeys[$pageViewKey] = [
                'start' => $date,
                'rt_si' => $beacons[$key]['rt_si'],
                'pid' => $beacons[$key]['pid'],
                'date' => $date,
            ];
        }

        return $this->pageViewUniqueKeys;
    }

    /**
     * @return array
     */
    public function extractVirtualViews(array $beacons)
    {
        foreach ($beacons as $key => $beacon) {
            if (false === $beacon) {
                continue;
            }

            $date = trim($beacon[0], "'");

            $beacons[$key] = json_decode(trim(ltrim($beacon[1], "'"), "'\n"), true);
            $beacons[$key]['date'] = $date;

            $pageViewKey = $this->_getPageViewKey($beacons[$key]);

            // We do not mark as page view beacons send when visitor leaves page
            if (isset($this->pageViewUniqueKeys[$pageViewKey])) {
                $this->pageViewUniqueKeys[$pageViewKey] = array_merge($this->pageViewUniqueKeys[$pageViewKey], ['end' => $date]);
                continue;
            }

            $this->pageViewUniqueKeys[$pageViewKey] = [
                'start' => $date,
                'rt_si' => $beacons[$key]['rt_si'],
                'pid' => $beacons[$key]['pid'],
                'date' => $date,
            ];
        }

        return $this->pageViewUniqueKeys;
    }

    /**
     * @return bool|string
     */
    private function _getPageViewKey(array &$data)
    {
        if (empty($data['rt_si'])) {
            $data['rt_si'] = 'missing_rt_session';
        }

        if (empty($data['pid'])) {
            $data['pid'] = 'missing';
        }

        if (empty($data['u'])) {
            return false;
        }

        return $data['rt_si'].$data['pid'].md5($data['u']);
    }
}
