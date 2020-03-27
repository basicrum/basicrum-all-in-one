<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\MainDataSelect;

interface MainDataInterface
{
    public function retrieve($connection, string $where, array $limitWhere): array;

    public function getCacheKeyPart(): string;
}
