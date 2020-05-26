<?php

declare(strict_types=1);

namespace App\BasicRum\DiagramSchema\Filters\Primary;

class DeviceType implements \App\BasicRum\DiagramSchema\Filters\FilterableInterface
{
    public function getSchema(): array
    {
        $schema = [
            'device_type' => [
                'type' => 'object',
                'properties' => [
                    'search_value' => [
                        'enum' => [1, 2, 3, 4, 5],
                        'type' => 'integer',
                    ],
                    'condition' => [
                        'enum' => ['is'],
                    ],
                ],
            ],
        ];

        return $schema;
    }
}
