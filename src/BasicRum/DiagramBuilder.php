<?php

declare(strict_types=1);

namespace App\BasicRum;

use App\BasicRum\Bucketizer;

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
     * @param $data
     * @return array
     */
    public function build($data)
    {
        $samples = $this->report->query($data);

        $bucketizer = new Bucketizer();

        $buckets = $bucketizer->bucketize($samples, 400);

        $diagramData = [
            'xValues' => array_keys($buckets),
            'yValues' => array_values($buckets),
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