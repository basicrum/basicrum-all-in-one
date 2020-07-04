<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\TechnicalMetrics\SessionId;

use App\BasicRum\CoreObjects\ReaderHintInterface;

class ReaderHint implements ReaderHintInterface
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
