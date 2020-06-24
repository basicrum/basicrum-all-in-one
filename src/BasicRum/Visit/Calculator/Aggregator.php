<?php

declare(strict_types=1);

namespace App\BasicRum\Visit\Calculator;

use App\BasicRum\Visit\Data\Fetch;

class Aggregator
{
    /** @var int */
    private $sessionDuration;

    /** @var array */
    private $groupedPageViews = [];

    /** @var Aggregator\Chunk */
    private $chunk;

    /** @var Fetch */
    private $fetch;

    /** @var Aggregator\Completed */
    private $completed;

    /** @var Aggregator\Duration */
    private $duration;

    /** @var string */
    private $lastRtsiInScan = '';

    /** @var int */
    private $lastPageViewInScan = 0;

    public function __construct(int $sessionDuration, Fetch $fetch)
    {
        $this->sessionDuration = $sessionDuration;
        $this->chunk = new Aggregator\Chunk();
        $this->completed = new Aggregator\Completed();
        $this->duration = new Aggregator\Duration();
        $this->fetch = $fetch;
    }

    /**
     * @return Aggregator
     */
    public function addPageView(array $pageView): self
    {
        $this->groupedPageViews[$pageView['rt_si']][$pageView['pageViewId']] = $pageView;

        if ($pageView['pageViewId'] > $this->lastPageViewInScan) {
            $this->lastPageViewInScan = $pageView['pageViewId'];
            $this->lastRtsiInScan = $pageView['rt_si'];
        }

        return $this;
    }

    public function generateVisits(array $notCompletedVisits): array
    {
        $visits = [];

        foreach ($this->groupedPageViews as $rt_si => $views) {
            ksort($views, SORT_NUMERIC);

            $chunks = $this->chunk->chunkenize($views, $this->sessionDuration);

            $firstChunkKey = array_key_first($chunks);
            $lastChunkKey = array_key_last($chunks);

            foreach ($chunks as $currentChunkKey => $chunk) {
                $beginId = $chunk['begin'];
                $endId = $chunk['end'];

                $visitId = isset($notCompletedVisits[$beginId]['visitId']) ? $notCompletedVisits[$beginId]['visitId'] : false;

                $completed = true;

                if ($currentChunkKey === $lastChunkKey) {
                    $completed = $this->completed->isVisitCompleted(
                        $views[$endId]['createdAt'],
                        $this->_getLastPageViewDateInScan(),
                        $this->sessionDuration
                    );
                }

                $afterLastVisitDuration = $this->_calculateAfterLastVisitDuration(
                    $firstChunkKey === $currentChunkKey,
                    $currentChunkKey,
                    $chunks,
                    $views
                );

                $visitDuration = $this->duration->calculatePageViewsDurationDuration(
                    $views[$beginId],
                    $views[$endId]
                );

                $visits[] = [
                    'visitId' => $visitId,
                    'rt_si' => $rt_si,
                    'pageViewsCount' => $this->_countPageViews($views, $beginId, $endId),
                    'firstPageViewId' => $beginId,
                    'lastPageViewId' => $endId,
                    'firstUrlId' => $views[$beginId]['urlId'],
                    'lastUrlId' => $views[$endId]['urlId'],
                    'visitDuration' => $visitDuration,
                    'afterLastVisitDuration' => $afterLastVisitDuration,
                    'completed' => $completed,
                ];
            }
        }

        return $visits;
    }

    private function _getLastPageViewDateInScan(): \DateTime
    {
        return $this->groupedPageViews[$this->lastRtsiInScan][$this->lastPageViewInScan]['createdAt'];
    }

    private function _countPageViews(array $views, int $beginViewId, int $endViewId): int
    {
        $count = 0;

        $viewIds = array_keys($views);

        foreach ($viewIds as $pageViewId) {
            if ($pageViewId > $endViewId) {
                break;
            }

            if ($pageViewId >= $beginViewId && $pageViewId <= $endViewId) {
                ++$count;
            }
        }

        return $count;
    }

    private function _calculateAfterLastVisitDuration(
        bool $isFirstChunk,
        int $currentChunkKey,
        array &$chunks,
        array &$views
    ): int {
        //var_dump(func_get_args());

        $beginPageView = $views[$chunks[$currentChunkKey]['begin']];

        if ($isFirstChunk) {
            $previousPageView = $this->fetch->fetchPreviousSessionPageView($beginPageView);

            if (empty($previousPageView)) {
                return 0;
            }

            return $this->duration->calculatePageViewsDurationDuration($previousPageView, $beginPageView);
        }

        $previousPageView = $views[$chunks[$currentChunkKey - 1]['end']];

        return $this->duration->calculatePageViewsDurationDuration($previousPageView, $beginPageView);
    }
}
