<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\TechnicalMetrics\RedirectsCount;

use App\BasicRum\CoreObjects\ReaderHintInterface;

class ReaderHint implements ReaderHintInterface
{
    public function getFieldName(): string
    {
        return 'redirects_count';
    }

    public function getTabledName(): string
    {
        return 'rum_data_flat';
    }
}
