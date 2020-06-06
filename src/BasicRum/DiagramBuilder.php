<?php

declare(strict_types=1);

namespace App\BasicRum;

class DiagramBuilder
{
    public function build(DiagramOrchestrator $diagramOrchestrator, array $params, Release $releaseRepository): array
    {
        $layout = new Diagram\View\Layout();
        $diagramData = [];

        $results = $diagramOrchestrator->process();
        $hasError = false;

        //var_dump($results);

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

                try {
                    foreach ($result as $time => $sample) {
                        $data[$time] = $sample['count'];

                        // Summing total visits per day. Used later for calculating percentage
                        $totalsCount[$time] = isset($totalsCount[$time]) ? ($totalsCount[$time] + $data[$time]) : $data[$time];
                    }
                } catch (\Throwable $e) {
                    $hasError = true;
                }

                $segmentSamples[$key] = $data;
            }

            foreach ($segmentSamples as $key => $data) {
                foreach ($data as $time => $c) {
                    if (0 == $totalsCount[$time]) {
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
                    $extraDiagramParams,
                    $hasError
            );
        }

        if ('time_series' === $renderType) {
            $extraLayoutParams = [];
            $extraDiagramParams = [];

            if (!empty($params['global']['presentation']['layout'])) {
                $extraLayoutParams = $params['global']['presentation']['layout'];
            }

            $dataForDiagram = [];

            try {
                foreach ($results as $key => $result) {
                    $extraDiagramParams[$key] = [];

                    $date1 = array_key_first($result);
                    $date2 = array_key_last($result);

                    $releasesArray = $releaseRepository->getAllReleasesBetweenDates($date1, $date2);

                    foreach ($result as $time => $data) {
                        $x = isset($data[0]['x']) ? $data[0]['x'] : 0;
                        $dataForDiagram[$key][$time] = $x;
                    }
                }
            } catch (\Throwable $e) {
                $hasError = true;
            }

            $view = new Diagram\View\RenderType\TimeSeries($layout);

            $diagramData = $view->build(
                    $dataForDiagram,
                    $params,
                    $extraLayoutParams,
                    $extraDiagramParams,
                    $hasError,
                    $releasesArray
            );
        }

        if ('plane' === $renderType) {
            $dataForDiagram = [];
            $extraLayoutParams = [];
            $extraDiagramParams = [];

            if (!empty($params['global']['presentation']['layout'])) {
                $extraLayoutParams = $params['global']['presentation']['layout'];
            }

            try {
                foreach ($results as $key => $result) {
                    $extraDiagramParams[$key] = [];

                    if (!empty($params['segments'][$key]['data_requirements']['technical_metrics'])) {
                        $metrics = array_keys($params['segments'][$key]['data_requirements']['technical_metrics']);
                        //if ($metrics[0] === 'first_paint') {
                        $histogram = new \App\BasicRum\Report\Data\Histogram();

                        $buckets = $histogram->generate($result);

                        foreach ($buckets as $time => $bucket) {
                            $dataForDiagram[$key][$time] = $bucket;
                        }
                        //}
                    }

                    if (!empty($params['segments'][$key]['data_requirements']['business_metrics'])) {
                        $metrics = array_keys($params['segments'][$key]['data_requirements']['business_metrics']);
                        if ('bounce_rate' === $metrics[0]) {
                            $bounceRateCalculator = new \App\BasicRum\Report\Data\BounceRate();

                            $buckets = $bounceRateCalculator->generate($result);

                            foreach ($buckets as $time => $bucket) {
                                $dataForDiagram[$key][$time] = $bucket;
                            }

                            $extraDiagramParams[$key] = ['yaxis' => 'y2'];

                            $extraLayoutParams['yaxis2'] = [
                                'overlaying' => 'y',
                                'side' => 'right',
                                'showgrid' => false,
                                'tickvals' => [25, 50, 60, 70, 80, 100],
                                'ticktext' => ['25 %', '50 %', '60 %', '70 %', '80 %', '100 %'],
                                'range' => [50, 85],
                                'fixedrange' => true,
                            ];

                            foreach ($dataForDiagram[$key] as $brkey => $v) {
                                // Add annotation only on every second
                                if (0 != $brkey % 1000) {
                                    continue;
                                }

                                $extraLayoutParams['annotations'][] = [
                                    'xref' => 'x',
                                    'yref' => 'y2',
                                    'x' => $brkey,
                                    'y' => $v,
                                    'xanchor' => 'center',
                                    'yanchor' => 'bottom',
                                    'text' => $v.'%',
                                    'showarrow' => false,
                                    'font' => [
                                        'family' => 'Arial',
                                        'size' => 12,
                                        'color' => 'black',
                                    ],
                                ];
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                $hasError = true;
            }

            $view = new Diagram\View\RenderType\Plane($layout);

            $diagramData = $view->build(
                    $dataForDiagram,
                    $params,
                    $extraLayoutParams,
                    $extraDiagramParams,
                    $hasError
            );
        }

        return $diagramData;
    }
}
