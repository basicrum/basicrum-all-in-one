<?php

declare(strict_types=1);

namespace App\BasicRum\Diagram\View\RenderType;

class Distribution
{
    /** @var \App\BasicRum\Diagram\View\Layout */
    private $layout;

    /**
     * Distribution constructor.
     */
    public function __construct(\App\BasicRum\Diagram\View\Layout $layout)
    {
        $this->layout = $layout;
    }

    public function build(array $samples, array $renderParams, array $extraLayoutParams, array $extraDiagramParams): array
    {
        $data = [
            'diagrams' => [],
            'layout' => array_merge($this->layout->getLayout(), $extraLayoutParams),
        ];

        foreach ($samples as $key => $d) {
            $data['diagrams'][] = [
                'x' => array_keys($d),
                'y' => array_values($d),
                'stackgroup' => 'device',
                'name' => $renderParams['segments'][$key]['presentation']['name'],
                'marker' => [
                    'color' => $renderParams['segments'][$key]['presentation']['color'],
                ],
            ];
        }

        return $data;
    }
}
