<?php

namespace App\BasicRum\Beacon\RumData;

use App\BasicRum\ResourceTimingDecompressor_v_0_3_4;
use App\Entity\Beacons;

class ResourceTiming
{
    public function fetchResources(int $rumDataId, \Doctrine\Bundle\DoctrineBundle\Registry $registry): array
    {
        /** @var Beacons $beacon */
        $beacon = $registry
            ->getRepository(Beacons::class)
            ->findOneBy(['rumDataId' => $rumDataId]);

        $beaconData = json_decode($beacon->getBeacon(), true);

        $resourceTimingsData = [];

        if (!empty($beaconData['restiming'])) {
            $resourceTimingCompressed = json_decode($beaconData['restiming'], true);

            $decompressor = new ResourceTimingDecompressor_v_0_3_4();

            $resourceTimingsData = $decompressor->decompressResources($resourceTimingCompressed);

            usort($resourceTimingsData, function ($a, $b) {
                return $a['startTime'] - $b['startTime'];
            });
        }

        return $resourceTimingsData;
    }
}
