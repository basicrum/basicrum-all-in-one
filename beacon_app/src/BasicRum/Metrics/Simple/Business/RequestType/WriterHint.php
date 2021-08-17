<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Business\RequestType;

use App\BasicRum\Metrics\Interfaces\WriterHintInterface;

class WriterHint implements WriterHintInterface
{
    public function getFieldName(): string
    {
        return 'request_type';
    }

    public function getTabledName(): string
    {
        return 'rum_data_flat';
    }
}
