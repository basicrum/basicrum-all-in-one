<?php

declare(strict_types=1);

namespace App\BasicRum\Diagram\Builder\RenderType;

use App\BasicRum\Diagram\View\Layout;
use App\BasicRum\Diagram\View\RenderType\TimeSeries as ViewRenderTypeTimeSeries;
use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Release;
use App\BasicRum\RenderTypeInterface;

class TimeSeries implements RenderTypeInterface
{
    private $params;
    private $results;
    private $hasError;
    private $dataForDiagram;
    private $extraLayoutParams;
    private $extraDiagramParams;
    private $releaseRepository;

    public function __construct(DiagramOrchestrator $diagramOrchestrator, array $params, Release $releaseRepository)
    {
        $this->results = $diagramOrchestrator->process();
        $this->hasError = false;
        $this->params = $params;

        $this->extraLayoutParams = $this->setExtraLayoutParams();
        $this->extraDiagramParams = [];
        $this->dataForDiagram = [];
        $this->releaseRepository = $releaseRepository;
    }

    public function build(): array
    {
        try {
            foreach ($this->results as $key => $result) {
                $this->extraDiagramParams[$key] = [];

                $date1 = array_key_first($result);
                $date2 = array_key_last($result);

                $releasesArray = $this->releaseRepository->getAllReleasesBetweenDates($date1, $date2);

                foreach ($result as $time => $data) {
                    $x = isset($data[0]['x']) ? $data[0]['x'] : 0;
                    $this->dataForDiagram[$key][$time] = $x;
                }
            }
        } catch (\Throwable $e) {
            $this->hasError = true;
        }

        $view = new ViewRenderTypeTimeSeries(new Layout());

        return $view->build(
            $this->dataForDiagram,
            $this->params,
            $this->extraLayoutParams,
            $this->extraDiagramParams,
            $this->hasError,
            $releasesArray
        );
    }

    /**
     * @param array $extraDiagramParams
     */
    private function setExtraLayoutParams(): array
    {
        if (!empty($this->params['global']['presentation']['layout'])) {
            return $this->params['global']['presentation']['layout'];
        }

        return [];
    }
}
