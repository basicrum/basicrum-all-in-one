<?php

declare(strict_types=1);

namespace App\BasicRum\DiagramSchema\TechnicalMetrics;

class DownloadTime implements \App\BasicRum\DiagramSchema\MetricsInterface
{
    public function getPossibleDataFlavorType(): array
    {
        return [
            'percentile',
            'histogram',
        ];
    }
}
