<?php

namespace App\BasicRum;

class ResourceSize
{
    /**
     * @return array
     */
    public function calculateSizes(array $resourceTimings)
    {
        $total = 0;

        $html = 0;
        $img = 0;
        $js = 0;
        $css = 0;

        foreach ($resourceTimings as $resource) {
            // We do not need this feature right now
            break;
            if ('html' === $resource['initiatorType']) {
                $html = $resource['encodedBodySize'];
            }

            if (strpos($resource['name'], '.js') > 0) {
                $js += $resource['encodedBodySize'];
            }

            if (strpos($resource['name'], '.png') > 0) {
                $img += $resource['encodedBodySize'];
            }

            if (strpos($resource['name'], '.jpg') > 0) {
                $img += $resource['encodedBodySize'];
            }

            if (strpos($resource['name'], '.gif') > 0) {
                $img += $resource['encodedBodySize'];
            }

            if (strpos($resource['name'], '.css') > 0) {
                $css += $resource['encodedBodySize'];
            }

            if (isset($resource['encodedBodySize'])) {
                $total += $resource['encodedBodySize'];
            }
        }

        $other = $total - ($css + $html + $img + $js);

        return [
            'css' => $this->calculatePercentage($total, $css),
            'html' => $this->calculatePercentage($total, $html),
            'image' => $this->calculatePercentage($total, $img),
            'font' => '0.00',
            'js' => $this->calculatePercentage($total, $js),
            'other' => $this->calculatePercentage($total, $other),
        ];
    }

    /**
     * @param $total
     * @param $part
     *
     * @return string
     */
    public function calculatePercentage($total, $part)
    {
        return 0 === $total ? 0 : number_format($part / ($total) * 100, 2);
    }
}
