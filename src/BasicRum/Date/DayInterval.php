<?php
namespace App\BasicRum\Date;

use DateTime;
use DatePeriod;
use DateInterval;

class DayInterval
{

    /**
     * @param string $fromDate
     * @param string $toDate
     * @return array
     */
    public function generateDayIntervals(string $fromDate, string $toDate)
    {
        $calendarDayFrom = $fromDate;
        $calendarDayTo = $toDate;

        $period = new DatePeriod(
            new DateTime($calendarDayFrom),
            new DateInterval('P1D'),
            new DateTime($calendarDayTo)
        );

        $betweenArr = [];

        foreach ($period as $key => $value) {
            $calendarDay = $value->format('Y-m-d');

            $nextDay = new DateTime($value->format('Y-m-d'));
            $nextDay = $nextDay->modify( '+1 day' );

            $betweenArr[] = [
                'start' => $calendarDay . ' 00:00:00',
                'end'   => $nextDay->format('Y-m-d')  . ' 00:00:00'
            ];
        }

        return $betweenArr;
    }

}