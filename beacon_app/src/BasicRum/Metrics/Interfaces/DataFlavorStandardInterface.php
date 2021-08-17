<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Interfaces;

interface DataFlavorStandardInterface
{
    public function getDataFlavorKey(): string;
}
