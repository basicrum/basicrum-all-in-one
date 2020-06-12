<?php

namespace App\Tests\BasicRum\Visit\Calculator;

use App\BasicRum\Visit\Calculator\Aggregator;
use PHPUnit\Framework\TestCase;

class AggregatorTest extends TestCase
{
    private function _getAggregator()
    {
        $fetchMock = $this
            ->getMockBuilder(\App\BasicRum\Visit\Data\Fetch::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new Aggregator(30, $fetchMock);
    }

    /**
     * @group visit_aggregator
     *
     * @throws \Exception
     */
    public function testAggregatorSameRtsiTwoSeparateVisits()
    {
        $aggregator = $this->_getAggregator();

        $pageViews = [
            [
                'rt_si' => 'test-2-closed-sessions',
                'createdAt' => new \DateTime('2018-10-25 13:32:33'),
                'rumDataId' => 2,
                'urlId' => 2,
            ],
            [
                'rt_si' => 'test-2-closed-sessions',
                'createdAt' => new \DateTime('2018-10-28 13:32:33'),
                'rumDataId' => 3,
                'urlId' => 1,
            ],
        ];

        foreach ($pageViews as $view) {
            $aggregator->addPageView($view);
        }

        $res = $aggregator->generateVisits([]);

        $this->assertEquals(
            [
                [
                    'visitId' => false,
                    'rt_si' => 'test-2-closed-sessions',
                    'pageViewsCount' => 1,
                    'firstPageViewId' => 2,
                    'lastPageViewId' => 2,
                    'firstUrlId' => 2,
                    'lastUrlId' => 2,
                    'visitDuration' => 0,
                    'afterLastVisitDuration' => 0,
                    'completed' => true,
                ],
                [
                    'visitId' => false,
                    'rt_si' => 'test-2-closed-sessions',
                    'pageViewsCount' => 1,
                    'firstPageViewId' => 3,
                    'lastPageViewId' => 3,
                    'firstUrlId' => 1,
                    'lastUrlId' => 1,
                    'visitDuration' => 0,
                    'afterLastVisitDuration' => 259200,
                    'completed' => false,
                ],
            ],
            $res
        );
    }

    /**
     * @group visit_aggregator
     *
     * @throws \Exception
     */
    public function testAggregatorSameRtsiTwoSeparateVisitsAttachPreviouslyNotClosed()
    {
        $aggregator = $this->_getAggregator();

        $pageViews = [
            [
                'rt_si' => 'test-2-closed-sessions',
                'createdAt' => new \DateTime('2018-10-25 13:32:33'),
                'rumDataId' => 2,
                'urlId' => 2,
            ],
            [
                'rt_si' => 'test-2-closed-sessions',
                'createdAt' => new \DateTime('2018-10-28 13:32:33'),
                'rumDataId' => 3,
                'urlId' => 1,
            ],
        ];

        foreach ($pageViews as $view) {
            $aggregator->addPageView($view);
        }

        $aggregator->addPageView(
            [
                'rt_si' => 'test-2-closed-sessions',
                'createdAt' => new \DateTime('2018-10-25 13:27:00'),
                'rumDataId' => 1,
                'urlId' => 1,
            ]
        );

        $notCompletedVisits = [
            1 => [
                'visitId' => 1,
                'rt_si' => 'test-2-closed-sessions',
                'pageViewsCount' => 1,
                'firstPageViewId' => 1,
                'lastPageViewId' => 1,
                'firstUrlId' => 1,
                'lastUrlId' => 1,
                'completed' => false,
            ],
        ];

        $res = $aggregator->generateVisits($notCompletedVisits);

        $this->assertEquals(
            [
                [
                    'visitId' => 1,
                    'rt_si' => 'test-2-closed-sessions',
                    'pageViewsCount' => 2,
                    'firstPageViewId' => 1,
                    'lastPageViewId' => 2,
                    'firstUrlId' => 1,
                    'lastUrlId' => 2,
                    'completed' => true,
                    'visitDuration' => 333,
                    'afterLastVisitDuration' => 0,
                ],
                [
                    'visitId' => false,
                    'rt_si' => 'test-2-closed-sessions',
                    'pageViewsCount' => 1,
                    'firstPageViewId' => 3,
                    'lastPageViewId' => 3,
                    'firstUrlId' => 1,
                    'lastUrlId' => 1,
                    'completed' => false,
                    'visitDuration' => 0,
                    'afterLastVisitDuration' => 259200,
                ],
            ],
            $res
        );
    }

    /**
     * @group visit_aggregator
     *
     * @throws \Exception
     */
    public function testCloseMoreThanOneChunkWithSameRtsiWhenFirstAndLastScanPageViewAreOutsideExpireRange()
    {
        $aggregator = $this->_getAggregator();

        $pageViews = [
            [
                'rt_si' => 'test-2-closed-session',
                'createdAt' => new \DateTime('2018-10-25 13:32:33'),
                'rumDataId' => 2,
                'urlId' => 2,
            ],
            [
                'rt_si' => 'test-2-closed-session',
                'createdAt' => new \DateTime('2018-10-25 13:37:33'),
                'rumDataId' => 3,
                'urlId' => 1,
            ],
            [
                'rt_si' => 'test-2-closed-session',
                'createdAt' => new \DateTime('2018-10-25 18:37:33'),
                'rumDataId' => 4,
                'urlId' => 1,
            ],
            [
                'rt_si' => 'last-in-duration-range',
                'createdAt' => new \DateTime('2018-10-25 20:40:33'),
                'rumDataId' => 5,
                'urlId' => 1,
            ],
        ];

        foreach ($pageViews as $view) {
            $aggregator->addPageView($view);
        }

        $notCompletedVisits = [];

        $res = $aggregator->generateVisits($notCompletedVisits);

        $this->assertEquals(
            [
                [
                    'visitId' => false,
                    'rt_si' => 'test-2-closed-session',
                    'pageViewsCount' => 2,
                    'firstPageViewId' => 2,
                    'lastPageViewId' => 3,
                    'firstUrlId' => 2,
                    'lastUrlId' => 1,
                    'completed' => true,
                    'visitDuration' => 300,
                    'afterLastVisitDuration' => 0,
                ],
                [
                    'visitId' => false,
                    'rt_si' => 'test-2-closed-session',
                    'pageViewsCount' => 1,
                    'firstPageViewId' => 4,
                    'lastPageViewId' => 4,
                    'firstUrlId' => 1,
                    'lastUrlId' => 1,
                    'completed' => true,
                    'visitDuration' => 0,
                    'afterLastVisitDuration' => 18000,
                ],
                [
                    'visitId' => false,
                    'rt_si' => 'last-in-duration-range',
                    'pageViewsCount' => 1,
                    'firstPageViewId' => 5,
                    'lastPageViewId' => 5,
                    'firstUrlId' => 1,
                    'lastUrlId' => 1,
                    'completed' => false,
                    'visitDuration' => 0,
                    'afterLastVisitDuration' => 0,
                ],
            ],
            $res
        );
    }

    /**
     * @group visit_aggregator
     *
     * @throws \Exception
     */
    public function testCloseOnlyOneChunkWithSameRtsiWhenFirstAndLastScanPageViewAreInDurationRange()
    {
        $aggregator = $this->_getAggregator();

        $pageViews = [
            [
                'rt_si' => 'test-1-closed-session',
                'createdAt' => new \DateTime('2018-10-25 13:32:33'),
                'rumDataId' => 2,
                'urlId' => 2,
            ],
            [
                'rt_si' => 'test-1-closed-session',
                'createdAt' => new \DateTime('2018-10-25 13:37:33'),
                'rumDataId' => 3,
                'urlId' => 1,
            ],
            [
                'rt_si' => 'test-1-closed-session',
                'createdAt' => new \DateTime('2018-10-25 18:37:33'),
                'rumDataId' => 4,
                'urlId' => 1,
            ],
            [
                'rt_si' => 'last-in-duration-range',
                'createdAt' => new \DateTime('2018-10-25 18:40:33'),
                'rumDataId' => 5,
                'urlId' => 1,
            ],
        ];

        foreach ($pageViews as $view) {
            $aggregator->addPageView($view);
        }

        $notCompletedVisits = [];

        $res = $aggregator->generateVisits($notCompletedVisits);

        $this->assertEquals(
            [
                [
                    'visitId' => false,
                    'rt_si' => 'test-1-closed-session',
                    'pageViewsCount' => 2,
                    'firstPageViewId' => 2,
                    'lastPageViewId' => 3,
                    'firstUrlId' => 2,
                    'lastUrlId' => 1,
                    'completed' => true,
                    'visitDuration' => 300,
                    'afterLastVisitDuration' => 0,
                ],
                [
                    'visitId' => false,
                    'rt_si' => 'test-1-closed-session',
                    'pageViewsCount' => 1,
                    'firstPageViewId' => 4,
                    'lastPageViewId' => 4,
                    'firstUrlId' => 1,
                    'lastUrlId' => 1,
                    'completed' => false,
                    'visitDuration' => 0,
                    'afterLastVisitDuration' => 18000,
                ],
                [
                    'visitId' => false,
                    'rt_si' => 'last-in-duration-range',
                    'pageViewsCount' => 1,
                    'firstPageViewId' => 5,
                    'lastPageViewId' => 5,
                    'firstUrlId' => 1,
                    'lastUrlId' => 1,
                    'completed' => false,
                    'visitDuration' => 0,
                    'afterLastVisitDuration' => 0,
                ],
            ],
            $res
        );
    }

    /**
     * @group visit_aggregator
     *
     * @throws \Exception
     */
    public function testAfterLastVisitDurationCalculatedAgainstPreviouslyCompletedVisit()
    {
        $fetchMock = $this
            ->getMockBuilder(\App\BasicRum\Visit\Data\Fetch::class)
            ->disableOriginalConstructor()
            ->setMethods(['fetchPreviousSessionPageView'])
            ->getMock();

        $counter = 0;

        $fetchMock
            ->expects($this->atLeastOnce())
            ->method('fetchPreviousSessionPageView')
            ->willReturnCallback(function () use (&$counter) {
                ++$counter;
                if (1 == $counter) {
                    return [
                        'rt_si' => 'first-closed-session',
                        'createdAt' => new \DateTime('2018-10-24 13:32:33'),
                        'rumDataId' => 1,
                        'urlId' => 1,
                    ];
                }

                return [];
            });

        $aggregator = new Aggregator(30, $fetchMock);

        $pageViews = [
            [
                'rt_si' => 'first-closed-session',
                'createdAt' => new \DateTime('2018-10-25 13:32:33'),
                'rumDataId' => 2,
                'urlId' => 1,
            ],
            [
                'rt_si' => 'last-in-duration-range',
                'createdAt' => new \DateTime('2018-10-25 18:40:33'),
                'rumDataId' => 3,
                'urlId' => 1,
            ],
        ];

        foreach ($pageViews as $view) {
            $aggregator->addPageView($view);
        }

        $res = $aggregator->generateVisits([]);

        $this->assertEquals(
            [
                [
                    'visitId' => false,
                    'rt_si' => 'first-closed-session',
                    'pageViewsCount' => 1,
                    'firstPageViewId' => 2,
                    'lastPageViewId' => 2,
                    'firstUrlId' => 1,
                    'lastUrlId' => 1,
                    'completed' => true,
                    'visitDuration' => 0,
                    'afterLastVisitDuration' => 86400,
                ],
                [
                    'visitId' => false,
                    'rt_si' => 'last-in-duration-range',
                    'pageViewsCount' => 1,
                    'firstPageViewId' => 3,
                    'lastPageViewId' => 3,
                    'firstUrlId' => 1,
                    'lastUrlId' => 1,
                    'completed' => false,
                    'visitDuration' => 0,
                    'afterLastVisitDuration' => 0,
                ],
            ],
            $res
        );
    }
}
