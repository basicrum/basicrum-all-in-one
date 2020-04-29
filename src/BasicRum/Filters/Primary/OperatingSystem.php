<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Primary;

class OperatingSystem extends AbstractFilter
{
    public function getPrimaryTableName(): string
    {
        return 'navigation_timings';
    }

    public function getPrimarySearchFieldName(): string
    {
        return 'os_id';
    }

    public function getSchema(): string
    {
        $schema = '
                                    "operating_system": {
                                        "type": "object",
                                        "properties": {
                                            "search_value": {
                                                "enum": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21],
                                                "type": "integer"
                                            },
                                            "condition": {
                                                "enum": ["is"]
                                            }
                                        }
                                    }
        ';

        return $schema;
    }
}
