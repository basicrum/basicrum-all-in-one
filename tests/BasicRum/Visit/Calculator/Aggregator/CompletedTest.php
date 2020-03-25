<?php

namespace App\Tests\BasicRum\Visit\Calculator\Aggregator;

use PHPUnit\Framework\TestCase;

use App\BasicRum\Visit\Calculator\Aggregator\Completed;


class CompleteTest extends TestCase
{

    public function testCompletedInWithExpiredDates()
    {
        $completed = new Completed();

        $duration = 30;
        $earlyDate = new \DateTime('2018-10-25 13:35:33');
        $laterDate = new \DateTime('2018-10-25 16:32:33');

        $this->assertEquals(
            true,
            $completed->isVisitCompleted($earlyDate, $laterDate, $duration)
        );
    }

}