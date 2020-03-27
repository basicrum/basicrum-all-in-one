<?php

declare(strict_types=1);

namespace App\BasicRum\Diagram\View;

class Color
{
    public function getColors(): array
    {
        return [
            0 => 'rgb(44, 160, 44)',
            1 => 'rgb(255, 127, 14)',
            2 => 'rgb(31, 119, 180)',
            3 => 'rgb(31, 119, 44)',
            4 => 'rgb(255, 119, 44)',
        ];
    }
}
