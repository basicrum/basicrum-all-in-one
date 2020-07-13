<?php

namespace App\BasicRum\Diagram\Builder\RenderType\Metrics;

use App\BasicRum\Report\Data\BounceRate;

class PlaneBusinessMetrics
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
        $this->extraLayoutParams = [];
    }

    public function proceed()
    {
        foreach ($this->results as $key => $result) {
            $this->extraDiagramParams[$key] = [];

            if ($this->isBounceRate($this->params['segments'][$key])) {
                $this->generateDataForDiagram($result, $key);
                $this->generateExtraDiagramParams($key);
                $this->generateExtraLayoutParams($key);
            }
        }
    }

    /**
     * @param $param $params['segments'][$key]
     */
    private function isBounceRate($param)
    {
        if (!empty($param['data_requirements']['business_metrics'])) {
            $metrics = array_keys($param['data_requirements']['business_metrics']);

            return 'bounce_rate' === $metrics[0];
        }

        return false;
    }

    private function generateDataForDiagram(array $result, string $key)
    {
        $bounceRateCalculator = new BounceRate();

        $buckets = $bounceRateCalculator->generate($result);

        foreach ($buckets as $time => $bucket) {
            $this->dataForDiagram[$key][$time] = $bucket;
            $this->generateExtraLayoutParams($key);
        }

        $this->extraDiagramParams[$key] = ['yaxis' => 'y2'];
    }

    private function generateExtraDiagramParams(string $key)
    {
        $this->extraDiagramParams[$key] = ['yaxis' => 'y2'];
    }

    private function generateExtraLayoutParams(string $key)
    {
        $this->extraLayoutParams['yaxis2'] = [
            'overlaying' => 'y',
            'side' => 'right',
            'showgrid' => false,
            'tickvals' => [25, 50, 60, 70, 80, 100],
            'ticktext' => ['25 %', '50 %', '60 %', '70 %', '80 %', '100 %'],
            'range' => [50, 85],
            'fixedrange' => true,
        ];

        foreach ($this->dataForDiagram[$key] as $brkey => $v) {
            // Add annotation only on every second
            if (0 != $brkey % 1000) {
                continue;
            }

            $this->extraLayoutParams['annotations'][] = [
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

    public function getDataForDiagram(): array
    {
        return $this->dataForDiagram;
    }

    public function getExtraDiagramParams(): array
    {
        return $this->extraDiagramParams;
    }

    public function getExtraLayoutParams(): array
    {
        return $this->extraDiagramParams;
    }
}
