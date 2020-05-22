<?php

declare(strict_types=1);

namespace App\BasicRum\DiagramSchema;

interface MetricsInterface
{
    public function getPossibleDataFlavorType(): array;
}
