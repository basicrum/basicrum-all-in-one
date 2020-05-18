<?php

declare(strict_types=1);

namespace App\BasicRum\DiagramSchema\TechnicalMetrics;

class Collaborator implements \App\BasicRum\DiagramSchema\CollaboratorsInterface
{
    /** @var array */
    private $technicalMetricsClassMap = [
        'first_paint' => TimeToFirstPaint::class,
        'load_event_end' => DocumentReady::class,
        'first_byte' => TimeToFirstByte::class,
        'last_blocking_resource' => LastBlockingResource::class,
        'ttfb' => Ttfb::class,
        'download_time' => DownloadTime::class,
        'total_img_size' => TotalImgSize::class,
        'total_js_compressed_size' => TotalJsCompressedSize::class,
        'number_js_files' => NumberJsFiles::class,
    ];

    public function getAllPossibleMetrics(): array
    {
        return $this->technicalMetricsClassMap;
    }
}
