<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Derived\Business\BrowserVersion;

use App\BasicRum\Metrics\Interfaces\WriterHintInterface;

class WriterHint implements WriterHintInterface
{
    public function getFieldName(): string
    {
        return 'browser_version';
    }

    public function getTabledName(): string
    {
        return 'rum_data_flat';
    }
}
