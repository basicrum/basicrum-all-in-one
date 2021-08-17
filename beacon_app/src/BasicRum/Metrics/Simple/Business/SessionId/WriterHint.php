<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Business\SessionId;

use App\BasicRum\Metrics\Interfaces\WriterHintInterface;

class WriterHint implements WriterHintInterface
{
    public function getFieldName(): string
    {
        return 'rt_si';
    }

    public function getTabledName(): string
    {
        return 'rum_data_flat';
    }
}
