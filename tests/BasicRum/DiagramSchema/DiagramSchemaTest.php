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
                                        ],
                                        'operating_system' => [
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
                                                ],
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
                                                ],
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
                                        ],
                                        'operating_system' => [
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
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'segments' => [
                    'type' => 'object',
                    'properties' => [
                        1 => [
                            '"$ref"' => '#/definitions/segment',
                        ],
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
                                        ],
                                        'operating_system' => [
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
                                                            'enum' => [
                                                                0 => 50,
                                                            ],
                                                            'type' => 'integer',
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
                                                            'enum' => [
                                                                0 => 50,
                                                            ],
                                                            'type' => 'integer',
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
                                                            'enum' => [
                                                                0 => 50,
                                                            ],
                                                            'type' => 'integer',
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
                                                            'enum' => [
                                                                0 => 50,
                                                            ],
                                                            'type' => 'integer',
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
                                                            'enum' => [
                                                                0 => 50,
                                                            ],
                                                            'type' => 'integer',
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
                                                            'enum' => [
                                                                0 => 50,
                                                            ],
                                                            'type' => 'integer',
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
                                                            'enum' => [
                                                                0 => 50,
                                                            ],
                                                            'type' => 'integer',
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
                                                            'enum' => [
                                                                0 => 50,
                                                            ],
                                                            'type' => 'integer',
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
                                                            'enum' => [
                                                                0 => 50,
                                                            ],
                                                            'type' => 'integer',
                                                        ],
                                                    ],
                                                ],
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
                                                            'enum' => [
                                                                0 => 50,
                                                            ],
                                                            'type' => 'integer',
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
                                                            'enum' => [
                                                                0 => 50,
                                                            ],
                                                            'type' => 'integer',
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
                                                            'enum' => [
                                                                0 => 50,
                                                            ],
                                                            'type' => 'integer',
                                                        ],
                                                    ],
                                                ],
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
                                        ],
                                        'operating_system' => [
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
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'segments' => [
                    'type' => 'object',
                    'properties' => [
                        1 => [
                            '"$ref"' => '#/definitions/segment',
                        ],
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
}
