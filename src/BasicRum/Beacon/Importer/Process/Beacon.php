<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process;

class Beacon
{

    private $navigationTimingsNormalizer;

    /** @var array */
    private $pageViewUniqueKeys = [];

    public function __construct()
    {
        $this->navigationTimingsNormalizer = new Beacon\NavigationTimingsNormalizer();
    }

    /**
     * @param array $beacons
     *
     * @return array
     */
    public function extract(array $beacons)
    {
        $data = [];

        foreach ($beacons as $key => $beacon) {
            if (false === $beacon
                || strpos($beacon[1], 'user_agent') === false
                || strpos($beacon[1], '"user_agent":""') !== false
            ) {
                continue;
            }

            $beacons[$key] = json_decode($beacon[1], true);

            $date = $beacons[$key]['created_at'];

            $pageViewKey = $this->_getPageViewKey($beacons[$key]);

            if ($pageViewKey === false) {
                unset($beacons[$key]);
                continue;
            }

            // We do not mark as page view beacons send when visitor leaves page
            if (isset ($this->pageViewUniqueKeys[$pageViewKey])) {
                $this->pageViewUniqueKeys[$pageViewKey] = array_merge($this->pageViewUniqueKeys[$pageViewKey], ['end' => $date]);
                continue;
            }

            $this->pageViewUniqueKeys[$pageViewKey] = ['start' => $date];

            $data[$key] = $this->navigationTimingsNormalizer->normalize($beacons[$key]);

            $data[$key]['beacon_string'] = $beacon[1];
        }

        return $data;
    }

    /**
     * @param array $beacons
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
            if (isset ($this->pageViewUniqueKeys[$pageViewKey])) {
                $this->pageViewUniqueKeys[$pageViewKey] = array_merge($this->pageViewUniqueKeys[$pageViewKey], ['end' => $date]);
                continue;
            }

            $this->pageViewUniqueKeys[$pageViewKey] = [
                'start' => $date,
                'guid'  => $beacons[$key]['guid'],
                'pid'   => $beacons[$key]['pid'],
                'date'  => $date
            ];
        }

        return $this->pageViewUniqueKeys;
    }

    /**
     * @param array $beacons
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
            if (isset ($this->pageViewUniqueKeys[$pageViewKey])) {
                $this->pageViewUniqueKeys[$pageViewKey] = array_merge($this->pageViewUniqueKeys[$pageViewKey], ['end' => $date]);
                continue;
            }

            $this->pageViewUniqueKeys[$pageViewKey] = [
                'start' => $date,
                'guid'  => $beacons[$key]['guid'],
                'pid'   => $beacons[$key]['pid'],
                'date'  => $date
            ];
        }

        return $this->pageViewUniqueKeys;
    }

    /**
     * @param array $data
     * @return bool|string
     */
    private function _getPageViewKey(array &$data)
    {
        if (empty($data['guid'])) {
            $data['guid'] = 'missing_guid';
        }


        if (empty($data['pid'])) {
            $data['pid'] = 'missing';
        }

        if (empty($data['u'])) {
            return false;
        }

        return $data['guid'] . $data['pid'] . md5($data['u']);
    }

}