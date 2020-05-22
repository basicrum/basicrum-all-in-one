<?php

declare(strict_types=1);

namespace App\BasicRum;

use PHPUnit\Framework\TestCase;

class DiagramSchemaTest extends TestCase
{
    public function testDistributionSchema()
    {
        $distributionSchema = [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'definitions' => [
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
                                'filters' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'device_type' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'search_value' => [
                                                    'enum' => [
                                                        0 => 1,
                                                        1 => 2,
                                                        2 => 3,
                                                        3 => 4,
                                                        4 => 5,
                                                    ],
                                                    'type' => 'integer',
                                                ],
                                                'condition' => [
                                                    'enum' => [
                                                        0 => 'is',
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'operating_system' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'search_value' => [
                                                    'enum' => [
                                                        0 => 1,
                                                        1 => 2,
                                                        2 => 3,
                                                        3 => 4,
                                                        4 => 5,
                                                        5 => 6,
                                                        6 => 7,
                                                        7 => 8,
                                                        8 => 9,
                                                        9 => 10,
                                                        10 => 11,
                                                        11 => 12,
                                                        12 => 13,
                                                        13 => 14,
                                                        14 => 15,
                                                        15 => 16,
                                                        16 => 17,
                                                        17 => 18,
                                                        18 => 19,
                                                        19 => 20,
                                                        20 => 21,
                                                    ],
                                                    'type' => 'integer',
                                                ],
                                                'condition' => [
                                                    'enum' => [
                                                        0 => 'is',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'oneOf' => [
                                        0 => [
                                            'required' => [
                                                0 => 'device_type',
                                            ],
                                        ],
                                        1 => [
                                            'required' => [
                                                0 => 'operating_system',
                                            ],
                                        ],
                                    ],
                                ],
                                'technical_metrics' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'first_paint' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'count' => [
                                                            'type' => 'boolean',
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'load_event_end' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'count' => [
                                                            'type' => 'boolean',
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'first_byte' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'count' => [
                                                            'type' => 'boolean',
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'last_blocking_resource' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'count' => [
                                                            'type' => 'boolean',
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'ttfb' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'count' => [
                                                            'type' => 'boolean',
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'download_time' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'count' => [
                                                            'type' => 'boolean',
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'total_img_size' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'count' => [
                                                            'type' => 'boolean',
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'total_js_compressed_size' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'count' => [
                                                            'type' => 'boolean',
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'number_js_files' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'count' => [
                                                            'type' => 'boolean',
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'oneOf' => [
                                        0 => [
                                            'required' => [
                                                0 => 'first_paint',
                                            ],
                                        ],
                                        1 => [
                                            'required' => [
                                                0 => 'load_event_end',
                                            ],
                                        ],
                                        2 => [
                                            'required' => [
                                                0 => 'first_byte',
                                            ],
                                        ],
                                        3 => [
                                            'required' => [
                                                0 => 'last_blocking_resource',
                                            ],
                                        ],
                                        4 => [
                                            'required' => [
                                                0 => 'ttfb',
                                            ],
                                        ],
                                        5 => [
                                            'required' => [
                                                0 => 'download_time',
                                            ],
                                        ],
                                        6 => [
                                            'required' => [
                                                0 => 'total_img_size',
                                            ],
                                        ],
                                        7 => [
                                            'required' => [
                                                0 => 'total_js_compressed_size',
                                            ],
                                        ],
                                        8 => [
                                            'required' => [
                                                0 => 'number_js_files',
                                            ],
                                        ],
                                    ],
                                ],
                                'business_metrics' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'bounce_rate' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'count' => [
                                                            'type' => 'boolean',
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'stay_on_page_time' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'count' => [
                                                            'type' => 'boolean',
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'page_views_count' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'count' => [
                                                            'type' => 'boolean',
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'oneOf' => [
                                        0 => [
                                            'required' => [
                                                0 => 'bounce_rate',
                                            ],
                                        ],
                                        1 => [
                                            'required' => [
                                                0 => 'stay_on_page_time',
                                            ],
                                        ],
                                        2 => [
                                            'required' => [
                                                0 => 'page_views_count',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
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
                                    'enum' => [
                                        0 => 'time_series',
                                        1 => 'distribution',
                                        2 => 'plane',
                                    ],
                                ],
                                'layout' => [
                                    'title' => 'Layout',
                                    'type' => 'object',
                                    'properties' => [
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
                                    ],
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
                                            'enum' => [
                                                0 => 'moving',
                                            ],
                                        ],
                                        'start' => [
                                            'title' => 'Start',
                                            'type' => 'integer',
                                            'minimum' => 0,
                                        ],
                                        'end' => [
                                            'Title' => 'End Date',
                                            'enum' => [
                                                0 => 'now',
                                            ],
                                        ],
                                    ],
                                ],
                                'filters' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'device_type' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'search_value' => [
                                                    'enum' => [
                                                        0 => 1,
                                                        1 => 2,
                                                        2 => 3,
                                                        3 => 4,
                                                        4 => 5,
                                                    ],
                                                    'type' => 'integer',
                                                ],
                                                'condition' => [
                                                    'enum' => [
                                                        0 => 'is',
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'operating_system' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'search_value' => [
                                                    'enum' => [
                                                        0 => 1,
                                                        1 => 2,
                                                        2 => 3,
                                                        3 => 4,
                                                        4 => 5,
                                                        5 => 6,
                                                        6 => 7,
                                                        7 => 8,
                                                        8 => 9,
                                                        9 => 10,
                                                        10 => 11,
                                                        11 => 12,
                                                        12 => 13,
                                                        13 => 14,
                                                        14 => 15,
                                                        15 => 16,
                                                        16 => 17,
                                                        17 => 18,
                                                        18 => 19,
                                                        19 => 20,
                                                        20 => 21,
                                                    ],
                                                    'type' => 'integer',
                                                ],
                                                'condition' => [
                                                    'enum' => [
                                                        0 => 'is',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'oneOf' => [
                                        0 => [
                                            'required' => [
                                                0 => 'device_type',
                                            ],
                                        ],
                                        1 => [
                                            'required' => [
                                                0 => 'operating_system',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'segments' => [
                    'type' => 'array',
                    'items' => [
                        '$ref' => '#/definitions/segment',
                    ],
                ],
            ],
        ];

        $schema = new DiagramSchema('distribution');
        $data = $schema->generateSchema();

        $this->assertEquals(
            $distributionSchema,
            $data
        );
    }

    public function testTimeSeriesSchema()
    {
        $timeSeriesSchema = [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'definitions' => [
                'segment' => [
                    'type' => 'object',
                    'properties' => [
                        'presentation' => [
                            'type' => 'object',
                            'properties' => [
                                'type' => [
                                    'enum' => [
                                        0 => 'bar',
                                    ],
                                ],
                            ],
                        ],
                        'data_requirements' => [
                            'type' => 'object',
                            'properties' => [
                                'filters' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'device_type' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'search_value' => [
                                                    'enum' => [
                                                        0 => 1,
                                                        1 => 2,
                                                        2 => 3,
                                                        3 => 4,
                                                        4 => 5,
                                                    ],
                                                    'type' => 'integer',
                                                ],
                                                'condition' => [
                                                    'enum' => [
                                                        0 => 'is',
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'operating_system' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'search_value' => [
                                                    'enum' => [
                                                        0 => 1,
                                                        1 => 2,
                                                        2 => 3,
                                                        3 => 4,
                                                        4 => 5,
                                                        5 => 6,
                                                        6 => 7,
                                                        7 => 8,
                                                        8 => 9,
                                                        9 => 10,
                                                        10 => 11,
                                                        11 => 12,
                                                        12 => 13,
                                                        13 => 14,
                                                        14 => 15,
                                                        15 => 16,
                                                        16 => 17,
                                                        17 => 18,
                                                        18 => 19,
                                                        19 => 20,
                                                        20 => 21,
                                                    ],
                                                    'type' => 'integer',
                                                ],
                                                'condition' => [
                                                    'enum' => [
                                                        0 => 'is',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'oneOf' => [
                                        0 => [
                                            'required' => [
                                                0 => 'device_type',
                                            ],
                                        ],
                                        1 => [
                                            'required' => [
                                                0 => 'operating_system',
                                            ],
                                        ],
                                    ],
                                ],
                                'technical_metrics' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'first_paint' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'percentile' => [
                                                            'type' => 'integer',
                                                            'minimum' => 0,
                                                            'maximum' => 100,
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'load_event_end' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'percentile' => [
                                                            'type' => 'integer',
                                                            'minimum' => 0,
                                                            'maximum' => 100,
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'first_byte' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'percentile' => [
                                                            'type' => 'integer',
                                                            'minimum' => 0,
                                                            'maximum' => 100,
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'last_blocking_resource' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'percentile' => [
                                                            'type' => 'integer',
                                                            'minimum' => 0,
                                                            'maximum' => 100,
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'ttfb' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'percentile' => [
                                                            'type' => 'integer',
                                                            'minimum' => 0,
                                                            'maximum' => 100,
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'download_time' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'percentile' => [
                                                            'type' => 'integer',
                                                            'minimum' => 0,
                                                            'maximum' => 100,
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'total_img_size' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'percentile' => [
                                                            'type' => 'integer',
                                                            'minimum' => 0,
                                                            'maximum' => 100,
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'total_js_compressed_size' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'percentile' => [
                                                            'type' => 'integer',
                                                            'minimum' => 0,
                                                            'maximum' => 100,
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'number_js_files' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'percentile' => [
                                                            'type' => 'integer',
                                                            'minimum' => 0,
                                                            'maximum' => 100,
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'oneOf' => [
                                        0 => [
                                            'required' => [
                                                0 => 'first_paint',
                                            ],
                                        ],
                                        1 => [
                                            'required' => [
                                                0 => 'load_event_end',
                                            ],
                                        ],
                                        2 => [
                                            'required' => [
                                                0 => 'first_byte',
                                            ],
                                        ],
                                        3 => [
                                            'required' => [
                                                0 => 'last_blocking_resource',
                                            ],
                                        ],
                                        4 => [
                                            'required' => [
                                                0 => 'ttfb',
                                            ],
                                        ],
                                        5 => [
                                            'required' => [
                                                0 => 'download_time',
                                            ],
                                        ],
                                        6 => [
                                            'required' => [
                                                0 => 'total_img_size',
                                            ],
                                        ],
                                        7 => [
                                            'required' => [
                                                0 => 'total_js_compressed_size',
                                            ],
                                        ],
                                        8 => [
                                            'required' => [
                                                0 => 'number_js_files',
                                            ],
                                        ],
                                    ],
                                ],
                                'business_metrics' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'bounce_rate' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'percentile' => [
                                                            'type' => 'integer',
                                                            'minimum' => 0,
                                                            'maximum' => 100,
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'stay_on_page_time' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'percentile' => [
                                                            'type' => 'integer',
                                                            'minimum' => 0,
                                                            'maximum' => 100,
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'page_views_count' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'percentile' => [
                                                            'type' => 'integer',
                                                            'minimum' => 0,
                                                            'maximum' => 100,
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'oneOf' => [
                                        0 => [
                                            'required' => [
                                                0 => 'bounce_rate',
                                            ],
                                        ],
                                        1 => [
                                            'required' => [
                                                0 => 'stay_on_page_time',
                                            ],
                                        ],
                                        2 => [
                                            'required' => [
                                                0 => 'page_views_count',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
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
                                    'enum' => [
                                        0 => 'time_series',
                                        1 => 'distribution',
                                        2 => 'plane',
                                    ],
                                ],
                                'layout' => [
                                    'title' => 'Layout',
                                    'type' => 'object',
                                    'properties' => [
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
                                        'barmode' => [
                                            'enum' => [
                                                0 => 'overlay',
                                            ],
                                        ],
                                    ],
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
                                            'enum' => [
                                                0 => 'moving',
                                            ],
                                        ],
                                        'start' => [
                                            'title' => 'Start',
                                            'type' => 'integer',
                                            'minimum' => 0,
                                        ],
                                        'end' => [
                                            'Title' => 'End Date',
                                            'enum' => [
                                                0 => 'now',
                                            ],
                                        ],
                                    ],
                                ],
                                'filters' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'device_type' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'search_value' => [
                                                    'enum' => [
                                                        0 => 1,
                                                        1 => 2,
                                                        2 => 3,
                                                        3 => 4,
                                                        4 => 5,
                                                    ],
                                                    'type' => 'integer',
                                                ],
                                                'condition' => [
                                                    'enum' => [
                                                        0 => 'is',
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'operating_system' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'search_value' => [
                                                    'enum' => [
                                                        0 => 1,
                                                        1 => 2,
                                                        2 => 3,
                                                        3 => 4,
                                                        4 => 5,
                                                        5 => 6,
                                                        6 => 7,
                                                        7 => 8,
                                                        8 => 9,
                                                        9 => 10,
                                                        10 => 11,
                                                        11 => 12,
                                                        12 => 13,
                                                        13 => 14,
                                                        14 => 15,
                                                        15 => 16,
                                                        16 => 17,
                                                        17 => 18,
                                                        18 => 19,
                                                        19 => 20,
                                                        20 => 21,
                                                    ],
                                                    'type' => 'integer',
                                                ],
                                                'condition' => [
                                                    'enum' => [
                                                        0 => 'is',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'oneOf' => [
                                        0 => [
                                            'required' => [
                                                0 => 'device_type',
                                            ],
                                        ],
                                        1 => [
                                            'required' => [
                                                0 => 'operating_system',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'segments' => [
                    'type' => 'array',
                    'items' => [
                        '$ref' => '#/definitions/segment',
                    ],
                ],
            ],
        ];

        $schema = new DiagramSchema('time_series');
        $data = $schema->generateSchema();

        $this->assertEquals(
            $timeSeriesSchema,
            $data
        );
    }

    public function testPlaneSchema()
    {
        $planeSchema = [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'definitions' => [
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
                                'filters' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'device_type' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'search_value' => [
                                                    'enum' => [
                                                        0 => 1,
                                                        1 => 2,
                                                        2 => 3,
                                                        3 => 4,
                                                        4 => 5,
                                                    ],
                                                    'type' => 'integer',
                                                ],
                                                'condition' => [
                                                    'enum' => [
                                                        0 => 'is',
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'operating_system' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'search_value' => [
                                                    'enum' => [
                                                        0 => 1,
                                                        1 => 2,
                                                        2 => 3,
                                                        3 => 4,
                                                        4 => 5,
                                                        5 => 6,
                                                        6 => 7,
                                                        7 => 8,
                                                        8 => 9,
                                                        9 => 10,
                                                        10 => 11,
                                                        11 => 12,
                                                        12 => 13,
                                                        13 => 14,
                                                        14 => 15,
                                                        15 => 16,
                                                        16 => 17,
                                                        17 => 18,
                                                        18 => 19,
                                                        19 => 20,
                                                        20 => 21,
                                                    ],
                                                    'type' => 'integer',
                                                ],
                                                'condition' => [
                                                    'enum' => [
                                                        0 => 'is',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'oneOf' => [
                                        0 => [
                                            'required' => [
                                                0 => 'device_type',
                                            ],
                                        ],
                                        1 => [
                                            'required' => [
                                                0 => 'operating_system',
                                            ],
                                        ],
                                    ],
                                ],
                                'technical_metrics' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'first_paint' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'histogram' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'bucket' => [
                                                                    'enum' => [
                                                                        0 => 200,
                                                                    ],
                                                                    'type' => 'integer',
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'load_event_end' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'histogram' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'bucket' => [
                                                                    'enum' => [
                                                                        0 => 200,
                                                                    ],
                                                                    'type' => 'integer',
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'first_byte' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'histogram' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'bucket' => [
                                                                    'enum' => [
                                                                        0 => 200,
                                                                    ],
                                                                    'type' => 'integer',
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'last_blocking_resource' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'histogram' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'bucket' => [
                                                                    'enum' => [
                                                                        0 => 200,
                                                                    ],
                                                                    'type' => 'integer',
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'download_time' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'histogram' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'bucket' => [
                                                                    'enum' => [
                                                                        0 => 200,
                                                                    ],
                                                                    'type' => 'integer',
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'oneOf' => [
                                        0 => [
                                            'required' => [
                                                0 => 'first_paint',
                                            ],
                                        ],
                                        1 => [
                                            'required' => [
                                                0 => 'load_event_end',
                                            ],
                                        ],
                                        2 => [
                                            'required' => [
                                                0 => 'first_byte',
                                            ],
                                        ],
                                        3 => [
                                            'required' => [
                                                0 => 'last_blocking_resource',
                                            ],
                                        ],
                                        4 => [
                                            'required' => [
                                                0 => 'download_time',
                                            ],
                                        ],
                                    ],
                                ],
                                'business_metrics' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'bounce_rate' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'histogram' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'bucket' => [
                                                                    'enum' => [
                                                                        0 => 200,
                                                                    ],
                                                                    'type' => 'integer',
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'stay_on_page_time' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'histogram' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'bucket' => [
                                                                    'enum' => [
                                                                        0 => 200,
                                                                    ],
                                                                    'type' => 'integer',
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'page_views_count' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data_flavor' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'histogram' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'bucket' => [
                                                                    'enum' => [
                                                                        0 => 200,
                                                                    ],
                                                                    'type' => 'integer',
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                    'oneOf' => [
                                                        0 => [
                                                            'required' => [
                                                                0 => 'percentile',
                                                            ],
                                                        ],
                                                        1 => [
                                                            'required' => [
                                                                0 => 'histogram',
                                                            ],
                                                        ],
                                                        2 => [
                                                            'required' => [
                                                                0 => 'count',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'oneOf' => [
                                        0 => [
                                            'required' => [
                                                0 => 'bounce_rate',
                                            ],
                                        ],
                                        1 => [
                                            'required' => [
                                                0 => 'stay_on_page_time',
                                            ],
                                        ],
                                        2 => [
                                            'required' => [
                                                0 => 'page_views_count',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
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
                                    'enum' => [
                                        0 => 'time_series',
                                        1 => 'distribution',
                                        2 => 'plane',
                                    ],
                                ],
                                'layout' => [
                                    'title' => 'Layout',
                                    'type' => 'object',
                                    'properties' => [
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
                                    ],
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
                                            'enum' => [
                                                0 => 'moving',
                                            ],
                                        ],
                                        'start' => [
                                            'title' => 'Start',
                                            'type' => 'integer',
                                            'minimum' => 0,
                                        ],
                                        'end' => [
                                            'Title' => 'End Date',
                                            'enum' => [
                                                0 => 'now',
                                            ],
                                        ],
                                    ],
                                ],
                                'filters' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'device_type' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'search_value' => [
                                                    'enum' => [
                                                        0 => 1,
                                                        1 => 2,
                                                        2 => 3,
                                                        3 => 4,
                                                        4 => 5,
                                                    ],
                                                    'type' => 'integer',
                                                ],
                                                'condition' => [
                                                    'enum' => [
                                                        0 => 'is',
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'operating_system' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'search_value' => [
                                                    'enum' => [
                                                        0 => 1,
                                                        1 => 2,
                                                        2 => 3,
                                                        3 => 4,
                                                        4 => 5,
                                                        5 => 6,
                                                        6 => 7,
                                                        7 => 8,
                                                        8 => 9,
                                                        9 => 10,
                                                        10 => 11,
                                                        11 => 12,
                                                        12 => 13,
                                                        13 => 14,
                                                        14 => 15,
                                                        15 => 16,
                                                        16 => 17,
                                                        17 => 18,
                                                        18 => 19,
                                                        19 => 20,
                                                        20 => 21,
                                                    ],
                                                    'type' => 'integer',
                                                ],
                                                'condition' => [
                                                    'enum' => [
                                                        0 => 'is',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'oneOf' => [
                                        0 => [
                                            'required' => [
                                                0 => 'device_type',
                                            ],
                                        ],
                                        1 => [
                                            'required' => [
                                                0 => 'operating_system',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'segments' => [
                    'type' => 'array',
                    'items' => [
                        '$ref' => '#/definitions/segment',
                    ],
                ],
            ],
        ];

        $schema = new DiagramSchema('plane');
        $data = $schema->generateSchema();

        $this->assertEquals(
            $planeSchema,
            $data
        );
    }
}
