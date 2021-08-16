<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Business\RequestType;

use App\BasicRum\Metrics\Interfaces\LabelInterface;


class Label implements LabelInterface
{

    public function labelValue(): string
    {
        return "Request Type";
    }

}
