<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Business\SessionLength;

use App\BasicRum\Metrics\Interfaces\WriterHintInterface;

class WriterHint implements WriterHintInterface
{
    public function getFieldName(): string
    {
        return 'rt_sl';
    }

    public function getTabledName(): string
    {
        return 'rum_data_flat';
    }
}
