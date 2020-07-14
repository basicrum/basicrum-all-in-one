<?php

namespace App\BasicRum\Diagram\Builder\RenderType;

use App\BasicRum\Diagram\View\Layout;
use App\BasicRum\Diagram\View\RenderType\Distribution as ViewRenderTypeDistribution;
use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Release;
use App\BasicRum\RenderTypeInterface;

class Distribution implements RenderTypeInterface
{
    private $hasError;
    private $params;
    private $results;
    private $totalsCount;
    private $segmentSamples;
    private $dataForDiagram;
    private $extraLayoutParams;
    private $extraDiagramParams;

    public function __construct(DiagramOrchestrator $diagramOrchestrator, array $params, Release $releaseRepository)
    {
        $this->results = $diagramOrchestrator->process();
        $this->totalsCount = [];
        $this->segmentSamples = [];
        $this->dataForDiagram = [];
        $this->extraDiagramParams = [];
        $this->extraLayoutParams = $this->setExtraLayoutParams();
        $this->params = $params;
        $this->hasError = false;
    }

    public function build(): array
    {
        foreach ($this->results as $key => $result) {
            $data = [];
            $this->extraDiagramParams[$key] = [];

            try {
                foreach ($result as $time => $sample) {
                    $data[$time] = $sample['count'];

                    // Summing total visits per day. Used later for calculating percentage
                    $this->totalsCount[$time] = $this->calculateTotalVisitsPerDay($time, $data);
                }
            } catch (\Throwable $e) {
                $this->hasError = true;
            }

            $this->segmentSamples[$key] = $data;
        }

        $this->generateDataForDiagram();

        $view = new ViewRenderTypeDistribution(new Layout());

        return $view->build(
            $this->dataForDiagram,
            $this->params,
            $this->extraLayoutParams,
            $this->extraDiagramParams,
            $this->hasError
        );
    }

    /**
     * @param array $extraDiagramParams
     */
    public function setExtraLayoutParams(): array
    {
        if (!empty($this->params['global']['presentation']['layout'])) {
            return $this->params['global']['presentation']['layout'];
        }

        return [];
    }

    private function calculateTotalVisitsPerDay($time, array $data)
    {
        if (isset($this->totalsCount[$time])) {
            return $this->totalsCount[$time] + $data[$time];
        }

        return $data[$time];
    }

    /**
     * @param $time
     */
    private function generateDataForDiagram(): void
    {
        foreach ($this->segmentSamples as $key => $data) {
            foreach ($data as $time => $c) {
                if (0 == $this->totalsCount[$time]) {
                    $time[$key][$time] = '0.00';
                    continue;
                }

                $this->dataForDiagram[$key][$time] = number_format(($c / $this->totalsCount[$time]) * 100, 2);
            }
        }
    }
}
