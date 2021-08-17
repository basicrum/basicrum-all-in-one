<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Derived\Business\DeviceManufacturer;

use App\BasicRum\Metrics\Interfaces\ReaderHintInterface;

class ReaderHint implements ReaderHintInterface
{
    public function getFieldName(): string
    {
        return 'device_manufacturer';
    }

    public function getTabledName(): string
    {
        return 'rum_data_flat';
    }
}
