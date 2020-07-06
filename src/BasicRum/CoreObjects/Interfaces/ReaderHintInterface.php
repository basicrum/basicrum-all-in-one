<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\Interfaces;

interface ReaderHintInterface
{
    public function getTabledName(): string;

    public function getFieldName(): string;
}
