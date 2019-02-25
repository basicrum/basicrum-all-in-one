<?php

declare(strict_types=1);

namespace App\BasicRum;

use App\BasicRum\Layers\Presentation;

class DiagramBuilder
{

    private $colors = [
        0 => 'rgb(44, 160, 44)',
        1 => 'rgb(255, 127, 14)',
        2 => 'rgb(31, 119, 180)',
        3 => 'rgb(31, 119, 44)',
        4 => 'rgb(255, 119, 44)'
    ];

    /**
     * @param array $buckets
     * @param CollaboratorsAggregator $collaboratorsAggregator
     * @return array
     */
    public function build(array $buckets, \App\BasicRum\CollaboratorsAggregator $collaboratorsAggregator) : array
    {
        $diagrams = [];

        $diagramData = [
            'x' => array_keys($buckets),
            'y' => array_values($buckets),
        ];

        $diagrams[] = array_merge(
            $diagramData,
            [
                'type' => 'line',
                'line' => ['color' => $this->colors[0]],
                'name' => 'Test name'
            ]
        );

        return [
            'diagrams'            => $diagrams,
            'layout_extra_shapes' => []
        ];
    }
//
//    /**
//     * @param array $data
//     * @return array
//     */
//    public function count(array $data)
//    {
//        $dayInterval = new DayInterval();
//
//        $interval = $dayInterval->generateDayIntervals(
//            $data['period']['current_period_from_date'],
//            $data['period']['current_period_to_date']
//        );
//
//        $samples = [];
//
//        foreach ($interval as $day) {
//            $samples[$day['start']] = count($this->report->query($day, $data['perf_metric'], $data['filters']));
//        }
//
//        return $samples;
//    }
//
//
//    /**
//     * @param array $data
//     *
//     * @return array
//     */
//    public function buildOverTime(array $data)
//    {
//        $bounceRateReport = new BounceRate($this->report->getDoctrine());
//
//        $bounceRateDay = [];
//
//        $dayInterval = new DayInterval();
//
//        $interval = $dayInterval->generateDayIntervals(
//            $data['period']['current_period_from_date'],
//            $data['period']['current_period_to_date']
//        );
//
//        $bucketizer = new Bucketizer();
//        $statisticMedian = new Median();
//
//        $median = [];
//
//        foreach ($interval as $day) {
//            $samples = $this->report->query($day, $data['perf_metric'], $data['filters']);
//            $median[$day['start']] = $statisticMedian->calculateMedian($bucketizer->bucketize($samples, 1));
//            $device = $data['filters']['device_type']['search_value'];
//            $bounceRateDay[$day['start']] = $bounceRateReport->bounceRateInPeriod($day['start'], $day['end'], $device);
//        }
//
//        $diagramData = [
//            'x' => array_keys($median),
//            'y' => array_values($median)
//        ];
//
//        $bounceRateDiagramData = [
//            'x' => array_keys($bounceRateDay),
//            'y' => array_values($bounceRateDay)
//        ];
//
//        return ['performance' => $diagramData, 'bounce_rate' => $bounceRateDiagramData];
//    }

}