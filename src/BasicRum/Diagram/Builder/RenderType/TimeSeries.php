<?php

namespace App\BasicRum\Diagram\Builder\RenderType;

use App\BasicRum\Diagram\View\Layout;
use App\BasicRum\Diagram\View\RenderType\TimeSeries as ViewRenderTypeTimeSeries;
use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Release;
use App\BasicRum\RenderTypeInterface;

class TimeSeries implements RenderTypeInterface
{
    public function build(DiagramOrchestrator $diagramOrchestrator, array $params, Release $releaseRepository): array
    {
        $layout = new Layout();

        $results = $diagramOrchestrator->process();
        $hasError = false;

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

        $view = new ViewRenderTypeTimeSeries($layout);

        return $view->build(
            $dataForDiagram,
            $params,
            $extraLayoutParams,
            $extraDiagramParams,
            $hasError,
            $releasesArray
        );
    }
}
