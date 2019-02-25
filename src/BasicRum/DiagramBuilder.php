<?php

declare(strict_types=1);

namespace App\BasicRum;

use App\BasicRum\Layers\Presentation;

class DiagramBuilder
{

    private $twoLevelDiagramsLayout = [
        'barmode' => 'overlay',
        'title'   => 'Time To First Paint vs Bounce Rate',
        'xaxis'=> [
            'rangemode' => 'tozero',
            'title' => 'First Paint (seconds)',
            'ticks' => 'outside',
            'tick0' => 0,
            'dtick' => 200,
            'ticklen' => 5,
            'tickwidth' => 2,
            'tickcolor' => '#000',
            'tickvals' => 'x1',
//            'ticktext' => '{{ x_axis_labels|raw }}',
            'fixedrange' => true
        ],
        'yaxis' => [
            'title' => 'Website Visits',
            'domain' => [0, 0.2],
            'fixedrange' => true
        ],
        'xaxis2' => [
            'anchor' => 'y2',
            'rangemode' => 'tozero',
            //title: 'Bounce Rate',
            //autotick: false,
            //ticks: 'outside',
            'tick0' => 0,
            'dtick' => 200,
            'ticklen' => 5,
            'tickwidth' => 2,
            'tickcolor' => '#000',
            'showgrid' => false,
            'zeroline' => false,
            'showline' => false,
            'autotick' => true,
            'ticks'    => '',
            'showticklabels' => false,
            'fixedrange' => true
        ],
        'yaxis2' => [
            'domain' => [0.3, 1],
            'fixedrange' => true
        ],
         'annotations' => [],
          'legend' => [
            'x' => 0,
            'y' => 1.2,
            'traceorder' => 'normal',
            'font' => [
                'family' => 'sans-serif',
                'size'   => 12,
                'color'  => '#000'
            ],
            'bgcolor' => '#E2E2E2',
            'bordercolor' => '#FFFFFF',
            'borderwidth' => 2
        ]
    ];

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
        $sampleDiagramValues = [];

        foreach ($buckets as $bucketSize => $bucket) {
            $sampleDiagramValues[$bucketSize] = count($bucket);
        }

        $samplesDiagram = [
            'x' => array_keys($sampleDiagramValues),
            'y' => array_values($sampleDiagramValues),
            'type' => 'bar',
            'name' => 'First Paint',
            'color' => $this->colors[0]
        ];

        $bounces  = [];

        foreach ($buckets as $bucketSize => $bucket) {
            $bounces[$bucketSize] = 0;
        }

        foreach ($buckets as $bucketSize => $bucket) {
            foreach ($bucket as $sample) {
                if ($sample['pageViewsCount'] == 1) {
                    $bounces[$bucketSize]++;
                }
            }
        }

        $bounceRate = [
            'x' => array_keys($bounces),
            'y' => array_values($bounces),
            'type' => 'scatter',
            'name' => 'Bounce Rate',
            'marker' => [
                'color' => 'rgb(255, 127, 14)'
            ],
            'xaxis' => 'x2',
            'yaxis' => 'y2'
        ];

        $layout = $this->attachSecondsToTimeLine($this->twoLevelDiagramsLayout, $buckets);

        return [
            'diagrams'            => [$samplesDiagram, $bounceRate],
            'layout_extra_shapes' => [],
            'layout'              => $layout
        ];
    }

    /**
     * @param array $layout
     * @param array $buckets
     * @return array
     */
    private function attachSecondsToTimeLine(array $layout, array $buckets) : array
    {
        $tickvals = [];

        foreach ($buckets as $bucketSize => $bucket) {
            if ($bucketSize % 1000 === 0) {
                $tickvals[$bucketSize] = $bucketSize / 1000 . ' sec';
            }
        }

        $layout['xaxis']['tickvals'] = array_keys($tickvals);
        $layout['xaxis']['ticktext'] = array_values($tickvals);

        return $layout;
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