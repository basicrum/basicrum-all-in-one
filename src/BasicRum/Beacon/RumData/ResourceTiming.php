<?php
namespace App\BasicRum\Beacon\RumData;

use App\Entity\Beacons;

use App\BasicRum\ResourceTimingDecompressor_v_0_3_4;

class ResourceTiming
{

    /**
     * @param int $pageViewId
     * @param \Symfony\Bridge\Doctrine\RegistryInterface $registry
     * @return array
     */
    public function fetchResources(int $pageViewId, \Symfony\Bridge\Doctrine\RegistryInterface $registry) : array
    {
        /** @var Beacons $beacon */
        $beacon = $registry
            ->getRepository(Beacons::class)
            ->findOneBy(['pageViewId' => $pageViewId]);

        $beaconData = json_decode($beacon->getBeacon(), true);

        $resourceTimingsData = [];

        if (!empty($beaconData['restiming'])) {

            $resourceTimingCompressed = json_decode($beaconData['restiming'], true);

            $decompressor = new ResourceTimingDecompressor_v_0_3_4();

            $resourceTimingsData = $decompressor->decompressResources($resourceTimingCompressed);

            usort($resourceTimingsData, function($a, $b) {
                return $a['startTime'] - $b['startTime'];
            });
        }

        return $resourceTimingsData;
    }


}
