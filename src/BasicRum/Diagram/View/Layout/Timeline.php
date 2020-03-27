<?php

declare(strict_types=1);

namespace App\BasicRum\Diagram\View\Layout;

class Timeline
{
    public function generateSeconds(int $start, int $end): array
    {
        $tickvals = [];

        for ($i = $start; $i * 1000 <= $end; ++$i) {
            $tickvals[$i * 1000] = $i.' sec';
        }

        return [
            'tickvals' => array_keys($tickvals),
            'ticktext' => array_values($tickvals),
        ];
    }
}
