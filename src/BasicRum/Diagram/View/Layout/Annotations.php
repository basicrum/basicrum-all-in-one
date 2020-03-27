<?php

declare(strict_types=1);

namespace App\BasicRum\Diagram\View\Layout;

class Annotations
{
    public function attachAnnotations(array $layout, array $diagram): array
    {
        foreach ($diagram as $key => $v) {
            // Add annotation only on every second
            if (0 != $v % 1000) {
                continue;
            }

            $layout['annotations'][] = [
                'xref' => 'x',
                'yref' => 'y2',
                'x' => $v,
                'y' => $diagram['y'][$key],
                'xanchor' => 'center',
                'yanchor' => 'bottom',
                'text' => $diagram['y'][$key].'%',
                'showarrow' => false,
                'font' => [
                    'family' => 'Arial',
                    'size' => 12,
                    'color' => 'black',
                ],
            ];
        }

        return $layout;
    }
}
