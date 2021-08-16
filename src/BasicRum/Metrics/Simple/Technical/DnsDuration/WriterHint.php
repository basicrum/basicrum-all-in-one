<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\DnsDuration;

use App\BasicRum\Metrics\Interfaces\WriterHintInterface;

class WriterHint implements WriterHintInterface
{
    public function getFieldName(): string
    {
        return 'dns_duration';
    }

    public function getTabledName(): string
    {
        return 'rum_data_flat';
    }
}
