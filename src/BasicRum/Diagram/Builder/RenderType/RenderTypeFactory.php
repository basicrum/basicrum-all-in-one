<?php

namespace App\BasicRum\Diagram\Builder\RenderType;

//use App\BasicRum\Diagram\Builder\RenderType\Plane;
//use App\BasicRum\Diagram\Builder\RenderType\Distribution;
//use App\BasicRum\Diagram\Builder\RenderType\TimeSeries;

use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Release;

class RenderTypeFactory
{
    private $renderType;

    private $renderTypeClassMap = [
        'plane' => Plane::class,
        'time_series' => TimeSeries::class,
        'distribution' => Distribution::class,
    ];

    public function __construct(string $type)
    {
        $this->renderType = $type;
    }

    public function buid(DiagramOrchestrator $diagramOrchestrator, array $params, Release $releaseRepository): array
    {
        //var_dump($this->renderTypeClassMap[$this->renderType]);
        $render = new $this->renderTypeClassMap[$this->renderType]();

        return $render->build($diagramOrchestrator, $params, $releaseRepository);
    }
}
