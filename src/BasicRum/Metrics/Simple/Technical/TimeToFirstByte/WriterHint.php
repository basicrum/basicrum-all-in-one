<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\TimeToFirstByte;

use App\BasicRum\Metrics\Interfaces\WriterHintInterface;

class WriterHint implements WriterHintInterface
{
    public function getFieldName(): string
    {
        return 'first_byte';
    }

    public function getTabledName(): string
    {
        return 'rum_data_flat';
    }
}
