<?php

declare(strict_types=1);

namespace App\BasicRum\Diagram\Builder\RenderType;

use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Release;
use App\BasicRum\RenderTypeInterface;

final class RenderTypeFactory
{
    public static function build(string $renderType, DiagramOrchestrator $diagramOrchestrator, array $params, Release $releaseRepository): RenderTypeInterface
    {
        $renderTypeClassMap = [
            'plane' => Plane::class,
            'time_series' => TimeSeries::class,
            'distribution' => Distribution::class,
        ];

        try {
            if (!\array_key_exists($renderType, $renderTypeClassMap)) {
                throw new \Exception('Unknown render type passed: '.$renderType);
            }
        } catch (\Throwable $e) {
            throw $e;
        }

        return new $renderTypeClassMap[$renderType]($diagramOrchestrator, $params, $releaseRepository);
    }
}
