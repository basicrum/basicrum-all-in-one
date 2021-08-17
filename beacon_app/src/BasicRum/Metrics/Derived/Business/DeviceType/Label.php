<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Derived\Business\DeviceType;

use App\BasicRum\Metrics\Interfaces\LabelInterface;


class Label implements LabelInterface
{

    public function labelValue(): string
    {
        return "Device Type";
    }

}
