<?php

declare(strict_types=1);

namespace App\BasicRum\Date;

use \DateTime;
use \DatePeriod;
use \DateInterval;

class DayInterval
{

    const TAIL_TIME = ' 00:00:00';

    /**
     * @param string $fromDate
     * @param string $toDate
     * @return array
     * @throws \Exception
     */
    public function generateDayIntervals(string $fromDate, string $toDate)
    {
        $betweenArr = [];

        if ($fromDate === $toDate) {
            $calendarDay = new DateTime($fromDate);

            $nextDay = new DateTime($fromDate);
            $nextDay = $nextDay->modify( '+1 day' );

            return [
                [
                    'start' => $calendarDay->format('Y-m-d') . self::TAIL_TIME,
                    'end'   => $nextDay->format('Y-m-d') . self::TAIL_TIME
                ]
            ];
        }

        $period = new DatePeriod(
            new DateTime($fromDate),
            new DateInterval('P1D'),
            new DateTime($toDate)
        );

        /** @var $value DateTime */
        foreach ($period as $key => $value) {
            $calendarDay = $value->format('Y-m-d');

            $nextDay = new DateTime($value->format('Y-m-d'));
            $nextDay = $nextDay->modify( '+1 day' );

            $betweenArr[] = [
                'start' => $calendarDay . self::TAIL_TIME,
                'end'   => $nextDay->format('Y-m-d')  . self::TAIL_TIME
            ];
        }

        return $betweenArr;
    }

}