<?php

declare(strict_types=1);

namespace App\BasicRum\Diagram;

use App\BasicRum\Statistics\Median;

class View
{

    /**
     * @param array $buckets
     * @param \App\BasicRum\CollaboratorsAggregator $collaboratorsAggregator
     * @return array
     */
    public function build(array $buckets, \App\BasicRum\CollaboratorsAggregator $collaboratorsAggregator) : array
    {
        $layoutBuilder = new View\Layout();

        $humanReadableTechnicalMetrics = [
            'loadEventEnd' => 'Document Ready',
            'firstPaint'   => 'Start Render',
            'firstByte'    => 'First Byte',
            'time'         => 'Time'
        ];

        $probesCount = 0;

        $diagrams = [];

        $usedTechnicalMetrics = $collaboratorsAggregator->getTechnicalMetrics()->getRequirements();
        $technicalMetricName = reset($usedTechnicalMetrics)->getSelectDataFieldName();

        $sampleDiagramValues = [];

        foreach ($buckets as $bucketSize => $bucket) {
            $sampleDiagramValues[$bucketSize] = count($bucket);
            $probesCount += $sampleDiagramValues[$bucketSize];
        }

        $samplesDiagram = [
            'x' => array_keys($sampleDiagramValues),
            'y' => array_values($sampleDiagramValues),
            'type' => 'bar',
            'name' => $humanReadableTechnicalMetrics[$technicalMetricName],
            'marker' => [
                'opacity'=> 0.6,
                'color' => '#ff4181'
            ]
        ];

        $diagrams[] = $samplesDiagram;

        $layout = $layoutBuilder->getLayout();
        $layout = $this->attachSecondsToTimeLine($layout, $buckets);

        foreach ($collaboratorsAggregator->getBusinessMetrics()->getRequirements() as $businessMetric) {
            if (strpos(get_class($businessMetric), 'BounceRate') !== false) {
                $bounceRateDiagramBuilder = new Presentation\BounceRate();

                $bounceRateDiagram = $bounceRateDiagramBuilder->generate($buckets);

                $diagrams[] = $bounceRateDiagramBuilder->generate($buckets);

                $layout['yaxis2'] = [
                    'overlaying' => 'y',
                    'side'       => 'right',
                    'showgrid'  => false,
                    'tickvals'   =>  [25, 50, 60, 70, 80, 100],
                    'ticktext'   => ['25 %', '50 %', '60 %', '70 %', '80 %', '100 %'],
                    'range'      => [50, 85],
                    'fixedrange' => true
                ];

                foreach ($bounceRateDiagram['x'] as $key => $v) {
                    // Add annotation only on every second
                    if ($v % 1000 != 0) {
                        continue;
                    }

                    $layout['annotations'][] = [
                        'xref'      => 'x',
                        'yref'      => 'y2',
                        'x'         =>  $v,
                        'y'         => $bounceRateDiagram['y'][$key],
                        'xanchor'   => 'center',
                        'yanchor'   => 'bottom',
                        'text'      => $bounceRateDiagram['y'][$key] . '%',
                        'showarrow' => false,
                        'font' => [
                            'family' => 'Arial',
                            'size'   => 12,
                            'color'  => 'black'
                        ]
                    ];
                }

            }
        }

        if (isset($_POST['decorators']['show_median'])) {
            $median = new Median();
            $medianVal = $median->calculateMedian($sampleDiagramValues);
            $layout['shapes'] = [
                [
                    'type' => 'line',
                    'x0' => $medianVal,
                    'y0' => 0,
                    'x1' => $medianVal,
                    'yref'=> 'paper',
                    'y1' => 1,
                    'line' => [
                        'color' => 'red',
                        'width' => 2.5,
                        'dash'  => 'dot'
                    ]
                ]
            ];
        }

        return [
            'diagrams' => $diagrams,
            'layout'   => $layout
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

}