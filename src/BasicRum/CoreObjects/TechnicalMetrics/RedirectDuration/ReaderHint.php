<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\TechnicalMetrics\RedirectDuration;

use App\BasicRum\CoreObjects\ReaderHintInterface;

class ReaderHint implements ReaderHintInterface
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
