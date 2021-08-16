<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Interfaces;

interface DataFlavorCustomInterface
{
    public function retrieve($connection, string $where, array $limitWhere): array;

    public function getCacheKeyPart(): string;
}
