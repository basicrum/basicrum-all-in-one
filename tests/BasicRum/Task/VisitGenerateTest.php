<?php

namespace App\Tests\BasicRum\Task;

use App\Tests\BasicRum\FixturesTestCase;

use App\BasicRum\Visit\Calculator;

class VisitGenerateTestCase extends FixturesTestCase
{

    protected function setUp()
    {
        static::bootKernel();
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    private function _getDoctrine() : \Doctrine\Bundle\DoctrineBundle\Registry
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
     * @param array $visits
     * @return array
     */
    private function _countSessions(array $visits)
    {
        $sessions = [];

        foreach ($visits as $visit) {
            $guid = $visit['guid'];
            $sessions[$guid] = isset($sessions[$guid]) ? $sessions[$guid] + 1 : 1;
        }

        return $sessions;
    }

    /**
     * @param array $visits
     * @return array
     */
    private function _countClosedSessions(array $visits)
    {
        $sessions = [];

        foreach ($visits as $visit) {
            $guid = $visit['guid'];

            if ($visit['completed']) {
                $sessions[$guid] = isset($sessions[$guid]) ? $sessions[$guid] + 1 : 1;
            }
        }

        return $sessions;
    }

}