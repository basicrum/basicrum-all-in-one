<?php

declare(strict_types=1);

namespace App\BasicRum;

interface RenderTypeInterface
{
    public function build(): array;
}
