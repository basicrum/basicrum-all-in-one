<?php

declare(strict_types=1);

namespace App\BasicRum\Diagram\Builder\RenderType\Metrics;

interface PlaneMetricsInterface
{
    public function proceed(int $key): void;

    public function getDataForDiagram(): array;

    public function getExtraDiagramParams(): array;

    public function getExtraLayoutParams(): ?array;
}
