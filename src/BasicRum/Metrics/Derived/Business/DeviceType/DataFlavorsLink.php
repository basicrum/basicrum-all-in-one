<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Derived\Business\DeviceType;

use App\BasicRum\Metrics\Interfaces\DataFlavorsInterface;

use App\BasicRum\Metrics\Constants\DataFlavors;

class DataFlavorsLink implements DataFlavorsInterface
{
    public function getDataFlavors(): array
    {
        return [
            DataFlavors::COUNT
        ];
    }
}
