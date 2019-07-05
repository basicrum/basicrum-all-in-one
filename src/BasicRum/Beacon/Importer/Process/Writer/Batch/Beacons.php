<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Writer\Batch;

class Beacons
{

    private $registry;

    /**
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $registry
     */
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param array $batch
     * @param int $lastPageViewId
     */
    public function batchInsert(array $batch, int $lastPageViewId)
    {
        $lastPageViewIdStartOffset = $lastPageViewId + 1;

        $mustFlush = false;

        foreach ($batch as $key => $entry) {
            $mustFlush = true;

            $pageViewId = $key + $lastPageViewIdStartOffset;

            $beacon = new \App\Entity\Beacons();

            $beacon->setPageViewId($pageViewId);
            $beacon->setBeacon($entry['beacon_string']);

            $this->registry->getManager()->persist($beacon);
        }

        if ($mustFlush) {
            $this->registry->getManager()->flush();
            $this->registry->getManager()->clear();
        }
    }

}