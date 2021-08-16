<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Business\RequestType;

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
