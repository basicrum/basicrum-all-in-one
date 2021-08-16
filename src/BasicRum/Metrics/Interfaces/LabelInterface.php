<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Interfaces;

interface LabelInterface
{
    public function labelValue(): string;

}
