<?php

declare(strict_types=1);

namespace App\BasicRum;

use App\BasicRum\Bucketizer;
use App\BasicRum\Densityzer;
use App\BasicRum\Statistics\Median;

class DiagramBuilder
{

    /**
     * @var Report
     */
    protected $report;

    public function __construct(Report $report)
    {
       $this->report = $report;
    }

    /**
     * @param array $data
     * @param int $bucketSize
     * @return array
     */
    public function build(array $data, int $bucketSize = 100)
    {
        $samples = $this->report->query($data['period'], $data['perf_metric']);

        $bucketizer = new Bucketizer();
        $densityzer = new Densityzer();
        $statisticMedian = new Median();

        $buckets = $bucketizer->bucketize($samples, $bucketSize);
        $densityBuckets = $densityzer->fillDensity($buckets, count($samples), 4);

        $diagramData = [
            'x' => array_keys($densityBuckets),
            'y' => array_values($densityBuckets),
            'median' => $statisticMedian->calculateMedian($bucketizer->bucketize($samples, 1))
        ];

        return $diagramData;
    }

    /**
     * @return array
     */
    public function getNavigationTimings()
    {
        return $this->report->getNavigationTimings();
    }

}