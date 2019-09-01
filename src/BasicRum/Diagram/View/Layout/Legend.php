<?php

declare(strict_types=1);

namespace App\BasicRum\Diagram\View\Layout;

class Legend
{

    /**
     * @return array
     */
    public function getLegend() : array
    {
        return [
            'traceorder' => 'normal',
            'font' => [
                'family' => 'sans-serif',
                'size'   => 12,
                'color'  => '#000'
            ],
            'x' => 0,
            'y' => 1.1,
            'orientation' => 'h'
        ];
    }


}