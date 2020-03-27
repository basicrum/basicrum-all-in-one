<?php

declare(strict_types=1);

namespace App\BasicRum\TechnicalMetrics;

class Collaborator implements \App\BasicRum\CollaboratorsInterface
{

    /** @var array */
    private $technicalMetricsClassMap = [
        'first_paint'             => TimeToFirstPaint::class,
        'load_event_end'          => DocumentReady::class,
        'first_byte'              => TimeToFirstByte::class,
        'last_blocking_resource'  => LastBlockingResource::class,
        'ttfb'                    => Ttfb::class,
        'download_time'           => DownloadTime::class,
        'total_img_size'          => TotalImgSize::class,
        'total_js_compressed_size'=> TotalJsCompressedSize::class,
        'number_js_files'         => NumberJsFiles::class,
    ];

    private $technicalMetrics = [];

    public function getCommandParameterName() : string
    {
        return 'technical_metrics';
    }

    /**
     * @param array $requirements
     * @return \App\BasicRum\CollaboratorsInterface
     */
    public function applyForRequirement(array $requirements) : \App\BasicRum\CollaboratorsInterface
    {
        foreach ($this->technicalMetricsClassMap as $filterKey => $class) {
            if (isset($requirements[$filterKey])) {
                $requirement = $requirements[$filterKey];

                if ($requirement == 1) {
                    /** @var \App\BasicRum\Report\SelectableInterface $filter */
                    $filter = new $class();

                    $this->technicalMetrics[$filterKey] = $filter;
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRequirements() : array
    {
        return $this->technicalMetrics;
    }

    /**
     * @return array
     */
    public function getAllPossibleRequirementsKeys() : array
    {
        return array_keys($this->technicalMetricsClassMap);
    }

}