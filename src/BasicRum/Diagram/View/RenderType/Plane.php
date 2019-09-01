<?php

declare(strict_types=1);

namespace App\BasicRum\Diagram\View\RenderType;

class Plane
{

    /** @var \App\BasicRum\Diagram\View\Layout */
    private $layout;

    /**
     * Distribution constructor.
     * @param \App\BasicRum\Diagram\View\Layout $layout
     */
    public function __construct(\App\BasicRum\Diagram\View\Layout $layout)
    {
        $this->layout = $layout;
    }

    /**
     * @param array $samples
     * @param array $renderParams
     * @param array $extraLayoutParams
     * @param array $extraDiagramParams
     * @return array
     */
    public function build(array $samples, array $renderParams, array $extraLayoutParams, array $extraDiagramParams) : array
    {
        $data = [
            'diagrams' => [],
            'layout'   => array_merge($this->layout->getLayout(), $extraLayoutParams)
        ];

        foreach ($samples as $key => $d) {
            $data['diagrams'][] = array_merge(
                [
                    'x' => array_keys($d),
                    'y' => array_values($d),
                    'name' => $renderParams['segments'][$key]['presentation']['name'],
                    'marker' => [
                        'color' => $renderParams['segments'][$key]['presentation']['color']
                    ]
                ],
                $extraDiagramParams[$key]
            );
        }

        return $data;
    }

}