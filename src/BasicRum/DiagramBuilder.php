<?php

declare(strict_types=1);

namespace App\BasicRum;

use App\BasicRum\Layers\Presentation;

class DiagramBuilder
{
    private $oneLevelDiagramsLayout = [
        'barmode' => 'overlay',
        'title'   => 'Time To First Paint vs Bounce Rate',
        'xaxis'=> [
            'rangemode' => 'tozero',
            'title' => '',
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
            'fixedrange' => true
        ],
        'legend' => [
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

    private $twoLevelDiagramsLayout = [
        'barmode' => 'overlay',
        'title'   => 'Time To First Paint vs Bounce Rate',
        'xaxis'=> [
            'rangemode' => 'tozero',
            'title' => '',
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
        $humanReadableTechnicalMetrics = [
            'loadEventEnd' => 'Document Ready',
            'firstPaint'   => 'Time To First Paint',
            'firstByte'    => 'Time To First Byte'
        ];

        $diagrams = [];

        $usedTechnicalMetrics = $collaboratorsAggregator->getTechnicalMetrics()->getRequirements();
        $technicalMetricName = reset($usedTechnicalMetrics)->getSelectDataFieldName();

        $sampleDiagramValues = [];

        foreach ($buckets as $bucketSize => $bucket) {
            $sampleDiagramValues[$bucketSize] = count($bucket);
        }

        $this->twoLevelDiagramsLayout['xaxis']['title'] = $humanReadableTechnicalMetrics[$technicalMetricName] .  ' seconds';

        $samplesDiagram = [
            'x' => array_keys($sampleDiagramValues),
            'y' => array_values($sampleDiagramValues),
            'type' => 'bar',
            'name' => $humanReadableTechnicalMetrics[$technicalMetricName],
            'color' => $this->colors[0]
        ];

        $diagrams[] = $samplesDiagram;

        foreach ($collaboratorsAggregator->getBusinessMetrics()->getRequirements() as $businessMetric) {
            if (strpos(get_class($businessMetric), 'BounceRate') !== false) {
                $bounceRateDiagramBuilder = new Presentation\BounceRate();
                $diagrams[] = $bounceRateDiagramBuilder->generate($buckets);

                $this->twoLevelDiagramsLayout['title'] = $humanReadableTechnicalMetrics[$technicalMetricName] .  ' vs. Bounce Rate';
            }
        }

        $this->oneLevelDiagramsLayout['title'] = $humanReadableTechnicalMetrics[$technicalMetricName] .  ' distribution';
        $layout = $this->attachSecondsToTimeLine($this->oneLevelDiagramsLayout, $buckets);

        if (count($diagrams) > 1) {
            $layout = $this->attachSecondsToTimeLine($this->twoLevelDiagramsLayout, $buckets);
        }


        return [
            'diagrams'            => $diagrams,
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

}