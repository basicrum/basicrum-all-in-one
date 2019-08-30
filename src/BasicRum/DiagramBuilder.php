<?php

declare(strict_types=1);

namespace App\BasicRum;

use App\BasicRum\Statistics\Median;

class DiagramBuilder
{

    private $_metricsCodeNameMapping = [
        'time_to_first_byte'     => 'first_byte',
        'time_to_first_paint'    => 'first_paint',
        'document_ready'         => 'load_event_end',
        //Too generic value. Probably in the future we need to prefix all values with entity name
        'last_blocking_resource' => 'time'
    ];

    /**
     * @param DiagramOrchestrator $diagramOrchestrator
     * @param array $params
     * @return array
     */
    public function build(DiagramOrchestrator $diagramOrchestrator, array $params) : array
    {
        $layout = new Diagram\View\Layout();
        $diagramData = [];

        $results = $diagramOrchestrator->process();

        $renderType = $params['global']['presentation']['render_type'];

        if ('distribution' === $renderType) {
            $totalsCount = [];
            $segmentSamples = [];
            $dataForDiagram = [];
            $extraLayoutParams = [];
            $extraDiagramParams = [];

            if (!empty($params['global']['presentation']['layout'])) {
                $extraLayoutParams = $params['global']['presentation']['layout'];
            }

            foreach ($results as $key => $result) {
                $data = [];
                $extraDiagramParams[$key] = [];

                foreach ($result as $time => $sample) {
                    $data[$time] = empty($sample[0]['count']) ? 0 : $sample[0]['count'];

                    // Summing total visits per day. Used later for calculating percentage
                    $totalsCount[$time] = isset($totalsCount[$time]) ? ($totalsCount[$time] + $data[$time]) : $data[$time];
                }

                $segmentSamples[$key] = $data;
            }


            foreach ($segmentSamples as $key => $data) {
                foreach ($data as $time => $c) {
                    if ($totalsCount[$time] == 0) {
                        $dataForDiagram[$key][$time] = '0.00';
                        continue;
                    }

                    $dataForDiagram[$key][$time] = number_format(($c / $totalsCount[$time]) * 100, 2);
                }
            }

            $view = new Diagram\View\RenderType\Distribution($layout);

            $diagramData = $view->build(
                $dataForDiagram,
                $params,
                $extraLayoutParams,
                $extraDiagramParams
            );
        }

        if ('time_series' === $renderType) {
            $bucketizer = new Buckets(1, 10000);
            $median = new Median();

            $extraLayoutParams = [];
            $extraDiagramParams = [];

            if (!empty($params['global']['presentation']['layout'])) {
                $extraLayoutParams = $params['global']['presentation']['layout'];
            }

            $dataForDiagram = [];

            foreach ($results as $key => $result) {
                $extraDiagramParams[$key] = [];
                $metrics = array_keys($params['segments'][$key]['data_requirements']['technical_metrics']);

                $searchKey = $this->_metricsCodeNameMapping[$metrics[0]] ?? '';

                foreach ($result as $time => $samples) {
                    $buckets = $bucketizer->bucketize($samples, $searchKey);
                    $countBuckets = [];

                    foreach ($buckets as $bucketSize => $bucket) {
                        $countBuckets[$bucketSize] = count($bucket);
                    }

                    $dataForDiagram[$key][$time] = $median->calculateMedian($countBuckets);
                }
            }

            $view = new Diagram\View\RenderType\TimeSeries($layout);

            $diagramData = $view->build(
                $dataForDiagram,
                $params,
                $extraLayoutParams,
                $extraDiagramParams
            );
        }

        if ('plane' === $renderType) {
            $bucketizer = new Buckets(200, 5000);

            $dataForDiagram = [];
            $extraLayoutParams = [];
            $extraDiagramParams = [];

            if (!empty($params['global']['presentation']['layout'])) {
                $extraLayoutParams = $params['global']['presentation']['layout'];
            }

            $searchKey = '';

            foreach ($results as $key => $result) {
                if (!empty($params['segments'][$key]['data_requirements']['technical_metrics'])) {
                    $metrics = array_keys($params['segments'][$key]['data_requirements']['technical_metrics']);
                    $searchKey = $this->_metricsCodeNameMapping[$metrics[0]] ?? '';
                    break;
                }
            }

            foreach ($results as $key => $result) {
                $extraDiagramParams[$key] = [];

                $buckets = $bucketizer->bucketizePeriod($result, $searchKey);

                if (!empty($params['segments'][$key]['data_requirements']['business_metrics'])) {
                    $metrics = array_keys($params['segments'][$key]['data_requirements']['business_metrics']);
                    if ($metrics[0] === 'bounce_rate') {
                        $bounceRateCalculator = new \App\BasicRum\Report\Data\BounceRate();

                        $dataForDiagram[$key] = $bounceRateCalculator->generate($buckets);

                        $extraDiagramParams[$key] = ['yaxis' => 'y2'];

                        $extraLayoutParams['yaxis2'] = [
                            'overlaying' => 'y',
                            'side'       => 'right',
                            'showgrid'  => false,
                            'tickvals'   =>  [25, 50, 60, 70, 80, 100],
                            'ticktext'   => ['25 %', '50 %', '60 %', '70 %', '80 %', '100 %'],
                            'range'      => [50, 85],
                            'fixedrange' => true
                        ];

                        foreach ($dataForDiagram[$key] as $brkey => $v) {
                            // Add annotation only on every second
                            if ($brkey % 1000 != 0) {
                                continue;
                            }

                            $extraLayoutParams['annotations'][] = [
                                'xref'      => 'x',
                                'yref'      => 'y2',
                                'x'         => $brkey,
                                'y'         => $v,
                                'xanchor'   => 'center',
                                'yanchor'   => 'bottom',
                                'text'      => $v . '%',
                                'showarrow' => false,
                                'font' => [
                                    'family' => 'Arial',
                                    'size'   => 12,
                                    'color'  => 'black'
                                ]
                            ];
                        }
                    }
                    continue;
                }

                foreach ($buckets as $time => $bucket) {
                    $dataForDiagram[$key][$time] = count($bucket);
                }
            }

            $view = new Diagram\View\RenderType\Plane($layout);

            $diagramData = $view->build(
                $dataForDiagram,
                $params,
                $extraLayoutParams,
                $extraDiagramParams
            );
        }

        return $diagramData;
    }

}