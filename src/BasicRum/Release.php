<?php

declare(strict_types=1);

namespace App\BasicRum;

use DateTime;

class Release
{
    private $repository;

    private $entityManager;

    public function setConnection(\App\Repository\ReleasesRepository $releases)
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
