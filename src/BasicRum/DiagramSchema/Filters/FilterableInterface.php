<?php

declare(strict_types=1);

namespace App\BasicRum\DiagramSchema\Filters;

interface FilterableInterface
{
    public function getSchema(): array;
}
