<?php

namespace App\BasicRum\Diagram\Builder\RenderType;

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

    public function build(DiagramOrchestrator $diagramOrchestrator, array $params, Release $releaseRepository): array
    {
        $render = new $this->renderTypeClassMap[$this->renderType]($diagramOrchestrator, $params, $releaseRepository);

        return $render->build($diagramOrchestrator, $params, $releaseRepository);
    }
}
