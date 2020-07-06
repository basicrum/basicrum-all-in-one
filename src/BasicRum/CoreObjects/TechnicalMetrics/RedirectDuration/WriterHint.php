<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\TechnicalMetrics\RedirectDuration;

use App\BasicRum\CoreObjects\Interfaces\WriterHintInterface;

class WriterHint implements WriterHintInterface
{
    public function getFieldName(): string
    {
        return 'redirect_duration';
    }

    public function getTabledName(): string
    {
        return 'rum_data_flat';
    }
}
