<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Secondary;

class BrowserName extends AbstractFilter
{
    public function getSecondaryTableName(): string
    {
        return 'navigation_timings_user_agents';
    }

    public function getSecondaryKeyFieldName(): string
    {
        return 'id';
    }

    public function getSecondarySearchFieldName(): string
    {
        return 'browser_name';
    }

    public function getPrimaryTableName(): string
    {
        return 'navigation_timings';
    }

    public function getPrimarySearchFieldName(): string
    {
        return 'user_agent_id';
    }

    public function getSchema(): ?string
    {
        return null;
    }
}
