<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Writer;

class Batch
{
    /** @var int */
    private $_batchSize;

    /** @var Batch\NavigationTimings */
    private $_navigationTimings;

    /** @var Batch\Beacons */
    private $_beacons;

    public function __construct(
        \Doctrine\Bundle\DoctrineBundle\Registry $registry,
        int $batchSize = 200
    ) {
        $this->_batchSize = $batchSize;

        $this->_navigationTimings = new Batch\NavigationTimings($registry);

        $this->_beacons = new Batch\Beacons($registry);
    }

    public function process(array $data)
    {
        $batch = [];

        $counter = 0;

        foreach ($data as $page) {
            ++$counter;

            $batch[] = $page;

            if (0 === $counter % $this->_batchSize) {
                $this->save($batch);
                unset($batch);
                $batch = [];
            }
        }

        // In case we have leftovers or initial batch wasn't completely fulfilled
        if (!empty($batch)) {
            $this->save($batch);
        }
    }

    private function save(array $views)
    {
        // We need this for offset when we insert in related tables
        $lastPageViewId = $this->_navigationTimings->getLastId();

        $this->_navigationTimings->batchInsert($views);
        $this->_beacons->batchInsert($views, $lastPageViewId);
    }
}
