<?php

declare(strict_types=1);

namespace App\BasicRum\Diagram\View;

class Layout
{

    /** @var Layout\Annotations */
    private $annotations;

    /** @var Layout\Legend */
    private $legend;

    /** @var Layout\Margin */
    private $margin;

    public function __construct()
    {
        $this->annotations = new Layout\Annotations();
        $this->legend      = new Layout\Legend();
        $this->margin      = new Layout\Margin();
        $this->timeline    = new Layout\Timeline();
    }

    /**
     * @return array
     */
    public function getLayout() : array
    {
        return [
            'height' => 280,
            'plot_bgcolor' => "#fcfcfc",
            'paper_bgcolor' => "#fcfcfc",
            'margin' => $this->margin->getMargin(),
            'xaxis' => [
                'fixedrange' => true
            ],
            'yaxis' => [
                'fixedrange' => true
            ],
            'xaxis2' => [
                'fixedrange' => true
            ],
            'yaxis2' => [
                'fixedrange' => true
            ],
            'legend' => $this->legend->getLegend()
        ];
    }


}