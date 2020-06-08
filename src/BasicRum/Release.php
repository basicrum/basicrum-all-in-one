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
     * @param string $startDate Start date
     * @param string $endDate   End date
     *
     * @return array [description]
     */
    public function getAllReleasesBetweenDates(string $startDate, string $endDate): array
    {
        return $this->releases->findAllBetweenDates(new DateTime($startDate), new DateTime($endDate));
    }
}
