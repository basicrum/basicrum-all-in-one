<?php

declare(strict_types=1);

namespace App\BasicRum;

use App\BasicRum\BusinessMetrics\Collaborator as BusinessMetrics;
use App\BasicRum\Filters\Collaborator as Filters;
use App\BasicRum\TechnicalMetrics\Collaborator as TechnicalMetrics;

class DiagramSchema
{
    private $definitionSegment;
    private $layout;
    private $filters;
    private $businessMetrics;
    private $technicalMetrics;

    /**
     * DiagramSchema constructor.
     *
     * @throws \Exception
     */
    public function __construct($type)
    {
        $this->type = $type;
        $this->generateLayout();

        $this->generateGlobalFilters();
        $this->generateDefinitionSegment();
    }

    public function getBusinessMetrics(): array
    {
        $class = new BusinessMetrics();
        $businessMetricsClassMap = $class->getAllPossibleRequirements();

        $count = \count($businessMetricsClassMap);

        $segmentMetricsPart = [
            'business_metrics' => [
                'type' => 'object',
                'properties' => [],
            ],
        ];

        foreach (array_keys($businessMetricsClassMap) as $key) {
            $segmentMetricsPart['business_metrics']['properties'][$key] = [
                'type' => 'object',
                'properties' => [],
            ];

            $segmentMetricsPart['business_metrics']['properties'][$key]['properties'] = $this->getDataFlavor($this->type);
        }

        return $segmentMetricsPart;
    }

    public function getTechnicalMetrics(): array
    {
        $class = new TechnicalMetrics();
        $technicalMetricsClassMap = $class->getAllPossibleRequirements();

        $segmentMetricsPart = [
            'technical_metrics' => [
                'type' => 'object',
                'properties' => [],
            ],
        ];

        foreach (array_keys($technicalMetricsClassMap) as $key) {
            $segmentMetricsPart['technical_metrics']['properties'][$key] = [
                'type' => 'object',
                'properties' => [],
            ];

            $segmentMetricsPart['technical_metrics']['properties'][$key]['properties'] = $this->getDataFlavor($this->type);
        }

        return $segmentMetricsPart;
    }

    public function getDataFlavor($renderType): array
    {
        $dataFlavor = '';
        if ('time_series' == $renderType) {
            $dataFlavor = [
                'data_flavor' => [
                    'type' => 'object',
                    'properties' => [
                        'percentile' => [
                            'enum' => [50],
                            'type' => 'integer',
                        ],
                    ],
                ],
            ];
        } elseif ('plane' == $renderType) {
            $dataFlavor = [
                'data_flavor' => [
                    'type' => 'object',
                    'properties' => [
                        'histogram' => [
                            'type' => 'object',
                            'properties' => [
                                'bucket' => [
                                    'enum' => [200],
                                    'type' => 'integer',
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        } elseif ('distribution' == $renderType) {
            $dataFlavor = [
                'data_flavor' => [
                    'type' => 'object',
                    'properties' => [
                        'count' => [
                            'type' => 'boolean',
                        ],
                    ],
                ],
            ];
        }

        return $dataFlavor;
    }

    public function generateDefinitionSegment()
    {
        $segment = [
            'segment' => [
                'type' => 'object',
                'properties' => [
                    'presentation' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => [
                                'type' => 'string',
                                'title' => 'Segment Name',
                            ],
                            'color' => [
                                'type' => 'string',
                                'title' => 'Segment Color',
                            ],
                        ],
                    ],
                    'data_requirements' => [
                        'type' => 'object',
                        'properties' => [
                            // technical metrics
                            // business metrics
                        ],
                    ],
                ],
            ],
        ];

        $businessMetrics = $this->getBusinessMetrics();
        $technicalMetrics = $this->getTechnicalMetrics();

        $segment['segment']['properties']['data_requirements']['properties'] = array_merge(
            $segment['segment']['properties']['data_requirements']['properties'],
            $this->filters,
            $technicalMetrics,
            $businessMetrics
        );

        if ('time_series' == $this->type) {
            $segment['segment']['properties']['presentation']['properties'] = [
                'type' => [
                    'enum' => ['bar'],
                ],
            ];
        }

        $this->definitionSegment = $segment;
    }

    public function generateLayout()
    {
        $this->layout = [
            'layout' => [
                'title' => 'Layout',
                'type' => 'object',
                'properties' => [
                    'bargap' => [
                        'description' => 'Bargap',
                        'type' => 'integer',
                        'minimum' => 0,
                    ],
                ],
            ],
        ];

        if ('time_series' == $this->type) {
            $barmode = [
                'barmode' => [
                    'enum' => ['overlay'],
                ],
            ];
            $this->layout = array_merge($this->layout, $barmode);
        }
    }

    public function generateGlobalFilters()
    {
        $filter = new Filters();
        $filtersClassMap = $filter->getAllPossibleRequirements();

        $schema = [
            'filters' => [
                'type' => 'object',
                'properties' => [],
            ],
        ];

        foreach ($filtersClassMap as $filterKey => $filtersClass) {
            $init = new $filtersClass();
            $filterSegment = $init->getSchema();

            if (\is_array($init->getSchema())) {
                $schema['filters']['properties'][key($filterSegment)] = $filterSegment;
            }
        }

        $this->filters = $schema;
    }

    public function generateSchema()
    {
        $schemaArray = [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'definitions' => [
            ],
            'type' => 'object',
            'properties' => [
                'global' => [
                    'type' => 'object',
                    'properties' => [
                        'presentation' => [
                            'title' => 'Presentation part',
                            'type' => 'object',
                            'properties' => [
                                'render_type' => [
                                    'title' => 'Widget Type',
                                    'enum' => ['time_series', 'distribution', 'plane'],
                                ],
                                // $this->layout
                                'layout' => [
                                    'title' => 'Layout',
                                    'type' => 'object',
                                    'properties' => [],
                                ],
                            ],
                        ],
                        'data_requirements' => [
                            'type' => 'object',
                            'properties' => [
                                'period' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'type' => [
                                            'title' => 'Type',
                                            'enum' => ['moving'],
                                        ],
                                        'start' => [
                                            'title' => 'Start',
                                            'type' => 'integer',
                                            'minimum' => 0,
                                        ],
                                        'end' => [
                                            'Title' => 'End Date',
                                            'enum' => ['now'],
                                        ],
                                    ],
                                ],
                                //$this->filters
                            ],
                        ],
                    ],
                ],
                'segments' => [
                    'type' => 'object',
                    'properties' => [
                        '1' => ['"$ref"' => '#/definitions/segment'],
                    ],
                ],
            ],
        ];

        if ($this->layout) {
            $schemaArray['properties']['global']['properties']['presentation']['properties']['layout']['properties'] = $this->layout;
        }

        $schemaArray['properties']['global']['properties']['data_requirements']['properties'] = array_merge(
            $schemaArray['properties']['global']['properties']['data_requirements']['properties'],
            $this->filters
        );

        $schemaArray['definitions'] = $this->definitionSegment;

        return $schemaArray;
    }
}
