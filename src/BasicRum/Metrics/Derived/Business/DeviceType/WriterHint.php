<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Derived\Business\DeviceType;

use App\BasicRum\Metrics\Interfaces\WriterHintInterface;

class WriterHint implements WriterHintInterface
{
    public function getFieldName(): string
    {
        return 'device_type';
    }

    public function getTabledName(): string
    {
        return 'rum_data_flat';
    }
}