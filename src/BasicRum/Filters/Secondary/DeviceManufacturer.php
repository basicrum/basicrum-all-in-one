<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Secondary;

class DeviceManufacturer extends AbstractFilter
{
    public function getSecondaryTableName(): string
    {
        return 'rum_data_user_agents';
    }

    public function getSecondaryKeyFieldName(): string
    {
        return 'id';
    }

    public function getSecondarySearchFieldName(): string
    {
        return 'device_manufacturer';
    }

    public function getPrimaryTableName(): string
    {
        return 'rum_data_flat';
    }

    public function getPrimarySearchFieldName(): string
    {
        return 'user_agent_id';
    }
}
