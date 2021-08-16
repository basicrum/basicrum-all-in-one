<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\RedirectDuration;

use App\BasicRum\Metrics\Interfaces\WriterHintInterface;

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
