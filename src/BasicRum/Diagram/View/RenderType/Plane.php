<?php

declare(strict_types=1);

namespace App\BasicRum\Diagram\View\RenderType;

class Plane
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

    public function build(array $samples, array $renderParams, array $extraLayoutParams, array $extraDiagramParams, bool $hasError): array
    {
        $data = [
            'diagrams' => [],
            'layout' => array_merge(
                    $this->layout->getLayout(),
                    $extraLayoutParams,
                    [
                        'xaxis' => [
                            'tickvals' => [0, 1000, 2000, 3000, 4000, 5000, 6000],
                            'ticktext' => ['0', '1 sec', '2 sec', '3 sec', '4 sec', '5 sec', '6 sec'],
                        ],
                    ]
            ),
        ];

        if ($hasError || empty($samples)) {
            $data['layout']['xaxis']['fixedrange'] = true;
            $data['layout']['xaxis']['range'] = [0, 6000];
            $data['layout']['xaxis']['tickvals'] = [0, 1000, 2000, 3000, 4000, 5000, 6000];
            $data['layout']['xaxis']['ticktext'] = ['0', '1 sec', '2 sec', '3 sec', '4 sec', '5 sec', '6 sec'];
            $data['layout']['yaxis']['fixedrange'] = true;
            $data['layout']['yaxis']['range'] = [0, 20];
            $data['layout']['annotations'][0]['xref'] = 'paper';
            $data['layout']['annotations'][0]['yref'] = 'paper';
            $data['layout']['annotations'][0]['text'] = $hasError ? 'Data error' : 'No data available';
            $data['layout']['annotations'][0]['showarrow'] = false;
            $data['layout']['annotations'][0]['font']['size'] = '14';
            $data['layout']['annotations'][0]['font']['color'] = '#ff0000';
        } else {
            foreach ($samples as $key => $d) {
                $data['diagrams'][] = array_merge(
                        [
                            'x' => array_keys($d),
                            'y' => array_values($d),
                            'name' => $renderParams['segments'][$key]['presentation']['name'],
                            'marker' => [
                                'color' => $renderParams['segments'][$key]['presentation']['color'],
                            ],
                            'type' => $renderParams['segments'][$key]['presentation']['type'] ?? 'line',
                        ],
                        $extraDiagramParams[$key]
                );
            }
        }

        return $data;
    }
}
