<?php

namespace App\BasicRum\Diagram\Builder\RenderType\Metrics;

use App\BasicRum\Report\Data\Histogram;

class PlaneTechnicalMetrics
{
    private $results;
    private $params;
    private $dataForDiagram;
    private $extraDiagramParams;
    private $extraLayoutParams;

    public function __construct(array $results, array $params)
    {
        $this->results = $results;
        $this->params = $params;
        $this->dataForDiagram = [];
        $this->extraDiagramParams = [];

        if (!empty($params['global']['presentation']['layout'])) {
            $this->extraLayoutParams = $params['global']['presentation']['layout'];
        }
    }

    public function proceed($key): void
    {
        $this->extraDiagramParams[$key] = [];

        if (!empty($this->params['segments'][$key]['data_requirements']['technical_metrics'])) {
            $this->generateDataForDiagram($this->results[$key], $key);
        }
    }

    private function generateBuckets(array $result): array
    {
        $histogram = new Histogram();

        return $histogram->generate($result);
    }

    private function generateDataForDiagram(array $result, string $key): void
    {
        foreach ($this->generateBuckets($result) as $time => $bucket) {
            $this->dataForDiagram[$key][$time] = $bucket;
        }
    }

    public function getDataForDiagram(): array
    {
        return $this->dataForDiagram;
    }

    public function getExtraDiagramParams(): array
    {
        return $this->extraDiagramParams;
    }

    /**
     * @return mixed
     */
    public function getExtraLayoutParams(): ?array
    {
        return $this->extraLayoutParams;
    }
}
