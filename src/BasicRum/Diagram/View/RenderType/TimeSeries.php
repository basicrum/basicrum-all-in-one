<?php

declare(strict_types=1);

namespace App\BasicRum\Diagram\View\RenderType;

class TimeSeries
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
            'layout' => array_merge($this->layout->getLayout(), $extraLayoutParams),
        ];

        if ($hasError || empty($samples)) {
            $data['layout']['xaxis']['fixedrange'] = true;
            $data['layout']['xaxis']['range'] = [0, 20];
            $data['layout']['yaxis']['fixedrange'] = true;
            $data['layout']['yaxis']['range'] = [0, 20];
            $data['layout']['annotations'][0]['xref'] = 'paper';
            $data['layout']['annotations'][0]['yref'] = 'paper';
            $data['layout']['annotations'][0]['text'] = $hasError ? 'Data error' : 'No data available';
            $data['layout']['annotations'][0]['showarrow'] = false;
            $data['layout']['annotations'][0]['font']['size'] = '14';
            $data['layout']['annotations'][0]['font']['color'] = '#ff0000';
        } else {
            $data['layout']['yaxis']['rangemode'] = 'tozero';
        }

        // adding line shape to mark the precise date of the release
        $data['layout']['shapes'][0]['type'] = 'line';
        $data['layout']['shapes'][0]['layer'] = 'above';

        $data['layout']['shapes'][0]['x0'] = '2019-07-21';
        $data['layout']['shapes'][0]['y0'] = 0;
        $data['layout']['shapes'][0]['x1'] = '2019-07-21';
        $data['layout']['shapes'][0]['y1'] = 497;
        //use y1 = 1 and yref = paper in order to have the annotation on top
        //$data['layout']['shapes'][0]['yref'] = 'paper';
        $data['layout']['shapes'][0]['line']['dash'] = 'dash';
        $data['layout']['shapes'][0]['line']['color'] = '#000';
        $data['layout']['shapes'][0]['line']['width'] = 3;

        //adding the annotation right next to the marker shape
        $data['layout']['annotations'][0]['visible'] = false;
        $data['layout']['annotations'][0]['x'] = '2019-07-21';
        //use y = 1 and yref = paper in order to have the annotation on top
        $data['layout']['annotations'][0]['y'] = 497;
        //$data['layout']['annotations'][0]['yref'] = 'paper';
        $data['layout']['annotations'][0]['text'] = 'Release v1.1';
        $data['layout']['annotations'][0]['showarrow'] = true;
        $data['layout']['annotations'][0]['ax'] = 0;
        $data['layout']['annotations'][0]['font']['size'] = '14';
        $data['layout']['annotations'][0]['font']['color'] = '#000';
        //annotation must be on some of the graphs in order to have clicktoshow working
        $data['layout']['annotations'][0]['clicktoshow'] = 'onoff';

        foreach ($samples as $key => $d) {
            $data['diagrams'][] = [
                'x' => array_keys($d),
                'y' => array_values($d),
                'type' => $renderParams['segments'][$key]['presentation']['type'],
                'name' => $renderParams['segments'][$key]['presentation']['name'],
                'marker' => [
                    'color' => $renderParams['segments'][$key]['presentation']['color'],
                ],
            ];
        }

        return $data;
    }
}
