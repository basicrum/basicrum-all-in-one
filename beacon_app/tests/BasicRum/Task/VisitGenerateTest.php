<?php

namespace App\Tests\BasicRum\Task;

use App\BasicRum\Visit\Calculator;
use App\Tests\BasicRum\FixturesTestCase;

class VisitGenerateTest extends FixturesTestCase
{
    protected function setUp()
    {
        static::bootKernel();
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    private function _getDoctrine(): \Doctrine\Bundle\DoctrineBundle\Registry
    {
        return static::$kernel->getContainer()->get('doctrine');
    }

    /**
     * @group visit_generate
     */
    public function testSeparatingVisitDurationWhenVisitExpiredMoreThanOnceInSameScannedChunk()
    {
        $calculator = new Calculator($this->_getDoctrine());
        $res = $calculator->calculate();

        $this->assertEquals(
            2,
            $this->_countSessions($res)['test-2-closed-sessions']
        );
    }

    /**
     * @group visit_generate
     */
    public function testClosingVisitThatHasNoOtherPageViewsButLastScannedViewForOtherVisitsHasGreaterTimeThanVisitExpireTime()
    {
        $calculator = new Calculator($this->_getDoctrine());
        $res = $calculator->calculate();

        $this->assertEquals(
            2,
            $this->_countClosedSessions($res)['test-2-closed-sessions']
        );
    }

    /**
     * @return array
     */
    private function _countSessions(array $visits)
    {
        $sessions = [];

        foreach ($visits as $visit) {
            $rtSi = $visit['rtSi'];
            $sessions[$rtSi] = isset($sessions[$rtSi]) ? $sessions[$rtSi] + 1 : 1;
        }

        return $sessions;
    }

    /**
     * @return array
     */
    private function _countClosedSessions(array $visits)
    {
        $sessions = [];

        foreach ($visits as $visit) {
            $rtSi = $visit['rtSi'];

            if ($visit['completed']) {
                $sessions[$rtSi] = isset($sessions[$rtSi]) ? $sessions[$rtSi] + 1 : 1;
            }
        }

        return $sessions;
    }
}
