<?php

declare(strict_types=1);

namespace App\BasicRum\DiagramSchema\TechnicalMetrics;

class TotalJsCompressedSize implements \App\BasicRum\DiagramSchema\MetricsInterface
{
    public function getPossibleDataFlavorType(): array
    {
        return [
            'percentile',
        ];
    }
}
