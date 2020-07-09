<?php

namespace App\BasicRum\Diagram\Builder\RenderType;

use App\BasicRum\Diagram\View\Layout;
use App\BasicRum\Diagram\View\RenderType\Plane as ViewRenderTypePlane;
use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Release;
use App\BasicRum\RenderTypeInterface;
use App\BasicRum\Report\Data\Histogram;

class Plane implements RenderTypeInterface
{
    private $diagramOrchestrator;
    private $params;
    private $releaseRepository;
    private $layout;
    private $dataForDiagram;
    private $extraDiagramParams;
    private $extraLayoutParams;

    public function __construct(DiagramOrchestrator $diagramOrchestrator, array $params, Release $releaseRepository)
    {
        $this->diagramOrchestrator = $diagramOrchestrator;
        $this->params = $params;
        $this->releaseRepository = $releaseRepository;
        $this->layout = new Layout();
        $this->dataForDiagram = [];
        $this->extraDiagramParams = [];
        $this->extraLayoutParams = $this->getExtraLayoutParams($params);
    }

    public function build(DiagramOrchestrator $diagramOrchestrator, array $params, Release $releaseRepository): array
    {
        $diagramData = [];

        $results = $diagramOrchestrator->process();
        $hasError = false;

//        $dataForDiagram = [];
//        $extraLayoutParams = [];
//        $extraDiagramParams = [];

//        $extraLayoutParams = $this->getExtraLayoutParams($params);

        try {
            foreach ($results as $key => $result) {
                $extraDiagramParams[$key] = [];

                if (!empty($params['segments'][$key]['data_requirements']['technical_metrics'])) {
                    $metrics = array_keys($params['segments'][$key]['data_requirements']['technical_metrics']);
                    //if ($metrics[0] === 'first_paint') {
                    $histogram = new Histogram();

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

        $view = new ViewRenderTypePlane($layout);

        $diagramData = $view->build(
            $dataForDiagram,
            $params,
            $extraLayoutParams,
            $extraDiagramParams,
            $hasError
        );

        return $diagramData;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    private function getExtraLayoutParams(?array $params): ?array
    {
        $extraLayoutParams = [];

        if (!empty($params['global']['presentation']['layout'])) {
            $extraLayoutParams = $params['global']['presentation']['layout'];
        }

        return $extraLayoutParams;
    }
}
