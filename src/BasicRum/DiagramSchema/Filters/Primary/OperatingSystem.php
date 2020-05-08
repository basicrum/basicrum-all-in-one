<?php

declare(strict_types=1);

namespace App\BasicRum\DiagramSchema\Filters\Primary;

class OperatingSystem implements \App\BasicRum\DiagramSchema\Filters\FilterableInterface
{
    public function getSchema(): array
    {
        $schema = [
            'operating_system' => [
                'type' => 'object',
                'properties' => [
                    'search_value' => [
                        'enum' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21],
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
