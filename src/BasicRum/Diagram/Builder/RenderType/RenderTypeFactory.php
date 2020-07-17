<?php

declare(strict_types=1);

namespace App\BasicRum\Diagram\Builder\RenderType;

use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Release;
use Exception;
use Throwable;

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

        try {
            $this->checkIfRenderTypeExits();
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function checkIfRenderTypeExits(): void
    {
        if (!\array_key_exists($this->renderType, $this->renderTypeClassMap)) {
            throw new Exception('No suitable render type factory found for: '.$this->renderType);
        }
    }

    public function build(DiagramOrchestrator $diagramOrchestrator, array $params, Release $releaseRepository): array
    {
        $render = new $this->renderTypeClassMap[$this->renderType]($diagramOrchestrator, $params, $releaseRepository);

        return $render->build();
    }
}
