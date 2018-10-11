<?php
namespace App\BasicRum\Date;

use DateTime;
use DatePeriod;
use DateInterval;

class DayInterval
{

    const TAIL_TIME = ' 00:00:00';

    /**
     * @param string $fromDate
     * @param string $toDate
     * @return array
     */
    public function generateDayIntervals(string $fromDate, string $toDate)
    {
        $betweenArr = [];

        $lastDay = new DateTime($toDate);
        $theDayAfter = $lastDay->modify('+1 day');
//        $theDayAfter = $lastDay;

        $period = new DatePeriod(
            new DateTime($fromDate),
            new DateInterval('P1D'),
            $theDayAfter
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

        // Add last day here
        $lastDay = new DateTime($toDate);


        return $betweenArr;
    }

}