<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\RedirectsCount;

use App\BasicRum\Metrics\Interfaces\WriterHintInterface;

class WriterHint implements WriterHintInterface
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
