<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\BusinessMetrics\SessionId;

use App\BasicRum\CoreObjects\Interfaces\WriterHintInterface;

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
