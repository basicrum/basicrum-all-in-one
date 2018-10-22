<?php

declare(strict_types=1);

namespace App\BasicRum;

use App\BasicRum\Report;

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
        $type = 'histogram';

        return $this->report->query($data);
    }

    /**
     * @return array
     */
    public function getNavigationTimings()
    {
        return $this->report->getNavigationTimings();
    }

}