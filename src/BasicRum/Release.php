<?php

declare(strict_types=1);

namespace App\BasicRum;

use DateTime;

class Release
{
    private $releasses;

    public function __construct(\App\Repository\ReleasesRepository $releases)
    {
        $this->releases = $releases;
    }

    /**
     * @param string $date1 Start date
     * @param string $date2 End date
     *
     * @return array [description]
     */
    public function getAllReleasesBetweenDates(string $date1, string $date2): array
    {
        $dateArray['startDate'] = new  DateTime($date1);
        $dateArray['endDate'] = new  DateTime($date2);

        return $this->releases->findAllBetweenDates($dateArray);
    }
}
