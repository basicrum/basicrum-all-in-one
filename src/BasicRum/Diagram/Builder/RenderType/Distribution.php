<?php

namespace App\BasicRum\Diagram\Builder\RenderType;

use App\BasicRum\Diagram\View\Layout;
use App\BasicRum\Diagram\View\RenderType\Distribution as ViewRenderTypeDistribution;
use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Release;
use App\BasicRum\RenderTypeInterface;

class Distribution implements RenderTypeInterface
{
    public function build(DiagramOrchestrator $diagramOrchestrator, array $params, Release $releaseRepository): array
    {
        $layout = new Layout();

        $results = $diagramOrchestrator->process();
        $hasError = false;

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

        $view = new ViewRenderTypeDistribution($layout);

        return $view->build(
            $dataForDiagram,
            $params,
            $extraLayoutParams,
            $extraDiagramParams,
            $hasError
        );
    }
}
