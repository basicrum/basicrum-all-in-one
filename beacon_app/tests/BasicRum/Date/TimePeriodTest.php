<?php

namespace App\Tests\BasicRum\Visit\Calculator;

use PHPUnit\Framework\TestCase;

use \DateTime;
use App\BasicRum\Date\TimePeriod;

class TimePeriodTest extends TestCase
{

    /**
     * @group time_period
     * @throws \Exception
     */
    public function testOneDay()
    {
        $timePeriod = new TimePeriod();

        $period = $timePeriod->getPastDaysFromNow(1);

        $start = new DateTime(date('Y-m-d', strtotime($period->getStart())));
        $end   = new DateTime(date('Y-m-d', strtotime($period->getEnd())));

        $diff = $start->diff($end)->days;

        $this->assertEquals(
            2,
            $diff
        );
    }

    /**
     * @group time_period
     * @throws \Exception
     */
    public function testFourDays()
    {
        $timePeriod = new TimePeriod();

        $period = $timePeriod->getPastDaysFromNow(4);

        $start = new DateTime(date('Y-m-d', strtotime($period->getStart())));
        $end   = new DateTime(date('Y-m-d', strtotime($period->getEnd())));

        $diff = $start->diff($end)->days;

        $this->assertEquals(
            5,
            $diff
        );
    }

    /**
     * @group time_period
     * @throws \Exception
     */
    public function testOneWeek()
    {
        $timePeriod = new TimePeriod();

        $period = $timePeriod->getPastWeeksFromNow(1);

        $start = new DateTime(date('Y-m-d', strtotime($period->getStart())));
        $end   = new DateTime(date('Y-m-d', strtotime($period->getEnd())));

        $diff = $start->diff($end)->days;

        $this->assertEquals(
            8,
            $diff
        );
    }

    /**
     * @group time_period
     * @throws \Exception
     */
    public function testFourWeeks()
    {
        $timePeriod = new TimePeriod();

        $period = $timePeriod->getPastWeeksFromNow(4);

        $start = new DateTime(date('Y-m-d', strtotime($period->getStart())));
        $end   = new DateTime(date('Y-m-d', strtotime($period->getEnd())));

        $diff = $start->diff($end)->days;

        $this->assertEquals(
            29,
            $diff
        );
    }

    /**
     * @group time_period
     * @throws \Exception
     */
    public function testOneMonth()
    {
        $timePeriod = new TimePeriod();

        $period = $timePeriod->getPastMonthsFromNow(1);

        $start = new DateTime(date('Y-m-d', strtotime($period->getStart())));
        $end   = new DateTime(date('Y-m-d', strtotime($period->getEnd())));

        $diff = $start->diff($end)->m;

        $this->assertEquals(
            1,
            $diff
        );
    }

    /**
     * @group time_period
     * @throws \Exception
     */
    public function testTwoMonths()
    {
        $timePeriod = new TimePeriod();

        $period = $timePeriod->getPastMonthsFromNow(2);

        $start = new DateTime(date('Y-m-d', strtotime($period->getStart())));
        $end   = new DateTime(date('Y-m-d', strtotime($period->getEnd())));

        $diff = $start->diff($end)->m;

        $this->assertEquals(
            2,
            $diff
        );
    }

}