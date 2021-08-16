<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\DnsDuration;

use App\BasicRum\Metrics\Interfaces\LabelInterface;


class Label implements LabelInterface
{

    public function labelValue(): string
    {
        return "Dns Duration";
    }

}
