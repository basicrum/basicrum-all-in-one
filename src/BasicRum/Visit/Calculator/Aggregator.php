<?php

declare(strict_types=1);

namespace App\BasicRum\Visit\Calculator;

class Aggregator
{

    /** @var int */
    private $sessionDuration;

    /** @var array */
    private $groupedPageViews = [];

    /** @var Aggregator\Chunk */
    private $chunk;

    /** @var Aggregator\Completed */
    private $completed;

    /** @var string */
    private $lastGuidInScan = '';

    /** @var int */
    private $lastPageViewInScan = 0;

    public function __construct(int $sessionDuration)
    {
        $this->sessionDuration = $sessionDuration;
        $this->chunk           = new Aggregator\Chunk();
        $this->completed = new Aggregator\Completed();


    }

    /**
     * @param array $pageView
     * @return Aggregator
     */
    public function addPageView(array $pageView) : self
    {
        $this->groupedPageViews[$pageView['guid']][$pageView['pageViewId']] = $pageView;

        if ($pageView['pageViewId'] > $this->lastPageViewInScan) {
            $this->lastPageViewInScan = $pageView['pageViewId'];
            $this->lastGuidInScan     = $pageView['guid'];
        }

        return $this;
    }

    /**
     * @param array $notCompletedVisits
     * @return array
     */
    public function generateVisits(array $notCompletedVisits) : array
    {
        $visits = [];

        foreach ($this->groupedPageViews as $guid => $views)
        {
            ksort($views, SORT_NUMERIC);

            $chunks = $this->chunk->chunkenize($views, $this->sessionDuration);

            $chunksCount = count($chunks);
            $counter = 0;

            foreach ($chunks as $chunk) {
                $counter++;

                $beginId = $chunk['begin'];
                $endId   = $chunk['end'];

                $visitId = isset($notCompletedVisits[$beginId]['visitId']) ? $notCompletedVisits[$beginId]['visitId'] : false;

                $completed = true;

                if ($counter === $chunksCount) {
                    $completed = $this->completed->isVisitCompleted(
                        $views[$endId]['createdAt'],
                        $this->_getLastPageViewDateInScan(),
                        $this->sessionDuration
                    );
                }

                $visits[] = [
                    'visitId'                => $visitId,
                    'guid'                   => $guid,
                    'pageViewsCount'         => $this->_countPageViews($views, $beginId, $endId),
                    'firstPageViewId'        => $beginId,
                    'lastPageViewId'         => $endId,
                    'firstUrlId'             => $views[$beginId]['urlId'],
                    'lastUrlId'              => $views[$endId]['urlId'],
                    'visitDuration'          => $views[$endId]['createdAt']->getTimestamp() - $views[$beginId]['createdAt']->getTimestamp(),
                    'afterLastVisitDuration' => 0,
                    'completed'              => $completed
                ];
            }
        }

        return $visits;
    }

    /**
     * @return \DateTime
     */
    private function _getLastPageViewDateInScan() : \DateTime
    {
        return $this->groupedPageViews[$this->lastGuidInScan][$this->lastPageViewInScan]['createdAt'];
    }

    /**
     * @param array $views
     * @param int $beginViewId
     * @param int $endViewId
     * @return int
     */
    private function _countPageViews(array $views, int $beginViewId, int $endViewId) : int
    {
        $count = 0;

        $viewIds = array_keys($views);

        foreach ($viewIds as $pageViewId) {
            if ($pageViewId > $endViewId) {
                break;
            }

            if ($pageViewId >= $beginViewId && $pageViewId <= $endViewId) {
                $count++;
            }
        }

        return $count;
    }

}