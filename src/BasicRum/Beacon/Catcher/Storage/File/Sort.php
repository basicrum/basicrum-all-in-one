<?php
declare(strict_types=1);

namespace App\BasicRum\Beacon\Catcher\Storage\File;

class Sort
{

    /**
     * @param array $beacons
     */
    public function sortBeacons(array &$beacons)
    {
        // Sort the array
        usort($beacons, function ($element1, $element2) {
            return $element1[0] - $element2[0];
        });
    }

}
