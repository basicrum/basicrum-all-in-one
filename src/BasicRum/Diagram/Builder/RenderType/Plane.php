<?php

namespace App\BasicRum\Diagram\Builder\RenderType;

use App\BasicRum\Diagram\Builder\RenderType\Metrics\PlaneBusinessMetrics;
use App\BasicRum\Diagram\Builder\RenderType\Metrics\PlaneTechnicalMetrics;
use App\BasicRum\Diagram\View\Layout;
use App\BasicRum\Diagram\View\RenderType\Plane as ViewRenderTypePlane;
use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Release;
use App\BasicRum\RenderTypeInterface;

class Plane implements RenderTypeInterface
{
    private $diagramOrchestrator;
    private $params;
    private $releaseRepository;
    private $layout;
    private $dataForDiagram;
    private $extraDiagramParams;
    private $extraLayoutParams;
    private $results;

    public function __construct(DiagramOrchestrator $diagramOrchestrator, array $params, Release $releaseRepository)
    {
//        print_r($params);
        $this->diagramOrchestrator = $diagramOrchestrator;
        $this->params = $params;
        $this->releaseRepository = $releaseRepository;
        $this->layout = new Layout();
        $this->dataForDiagram = [];
        $this->extraDiagramParams = [];
        $this->extraLayoutParams = $this->getExtraLayoutParams($params);
        $this->results = $diagramOrchestrator->process();
    }

    public function build(DiagramOrchestrator $diagramOrchestrator, array $params, Release $releaseRepository): array
    {
        $hasError = false;

        try {
            $businessMetrics = new PlaneBusinessMetrics($this->results, $params);
            $technicalMetrics = new PlaneTechnicalMetrics($this->results, $params);
            foreach ($this->results as $key => $result) {
                $businessMetrics->proceed($key);
                $technicalMetrics->proceed($key);
            }

            $this->setProperty($this->dataForDiagram, $businessMetrics->getDataForDiagram());
            $this->setProperty($this->extraDiagramParams, $businessMetrics->getExtraDiagramParams());
            $this->setProperty($this->extraLayoutParams, $businessMetrics->getExtraLayoutParams());

            $this->setProperty($this->dataForDiagram, $technicalMetrics->getDataForDiagram());
            $this->setProperty($this->extraDiagramParams, $technicalMetrics->getExtraDiagramParams());
            $this->setProperty($this->extraLayoutParams, $technicalMetrics->getExtraLayoutParams());
        } catch (\Throwable $e) {
            echo $e->getMessage().PHP_EOL;
            echo $e->getFile().': '.$e->getLine().PHP_EOL;
            $hasError = true;
        }

        $view = new ViewRenderTypePlane($this->layout);

        // It has to be sorted. Because order here has huge impact
        // probably object would be more suitable here
        ksort($this->dataForDiagram);

        return $view->build(
            $this->dataForDiagram,
            $this->params,
            $this->extraLayoutParams,
            $this->extraDiagramParams,
            $hasError
        );
    }

    public function setExtraDiagramParams(array $extraDiagramParams): void
    {
        $this->extraDiagramParams = array_merge($this->extraDiagramParams, $extraDiagramParams);
    }

    private function setProperty(&$property, ?array $data)
    {
        if (!empty($data) && \is_array($data)) {
            foreach ($data as $index => $value) {
                $property[$index] = $value;
            }
        }
    }

    public function setDataForDiagram(array $dataForDiagram): void
    {
//        $this->dataForDiagram = array_merge($this->dataForDiagram, $dataForDiagram);
        foreach ($dataForDiagram as $key => $value) {
            $this->dataForDiagram[$key] = $value;
        }
    }

    public function setExtraLayoutParams(?array $extraLayoutParams): void
    {
        $this->extraLayoutParams = array_merge($this->extraLayoutParams, $extraLayoutParams);
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
