<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Primary;

class DeviceType extends AbstractFilter
{
    public function getPrimaryTableName(): string
    {
        return 'navigation_timings';
    }

    public function getPrimarySearchFieldName(): string
    {
        return 'device_type_id';
    }

    public function getSchema(): string
    {
        $schema = '
                                    "device_type": {
                                        "type": "object",
                                        "properties": {
                                            "search_value": {
                                                "enum": [1, 2, 3, 4, 5],
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
