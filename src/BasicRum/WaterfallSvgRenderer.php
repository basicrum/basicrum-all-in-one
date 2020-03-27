<?php

namespace App\BasicRum;

class WaterfallSvgRenderer
{
    private $maxLength = 10000;

    private $_lineHeight = 18;

    /**
     * To HTML.
     *
     * @param array $navigationTimings
     *
     * @return string
     */
    public function render($navigationTimings)
    {
        $linesCount = \count($navigationTimings['restiming']);

        $waterfallHeight = $linesCount * $this->_lineHeight;

        $output = '<svg class="water-fall-chart" height="'.$waterfallHeight.'">';

        $output .= $this->renderTimeLines($waterfallHeight, $navigationTimings);

        $output .= $this->renderWaterfall($navigationTimings['restiming']);

        $output .= '</svg>';

        return $output;
    }

    /**
     * Render tree (recursive function).
     *
     * @return string
     */
    public function renderWaterfall(array $data)
    {
        $output = '<g class="rows-holder" height="700">';

        foreach ($data as $key => $resData) {
            $zebraClass = 'even';

            if ($key % 2 > 0) {
                $zebraClass = 'odd';
            }

            $urlParts = explode('/', $resData['name']);
            $url = $urlParts[\count($urlParts) - 1];

            $output .= '<a class="row-item" tabindex="0" xlink:href="javascript:void(0)" transform="translate(0, 0)">';
            $output .= '<clipPath id="titleFullClipPath"><rect height="100%" width="100%"></rect></clipPath>';
            $output .= '<rect class="'.$zebraClass.'" height="'.$this->_lineHeight.'" width="100%" x="0" y="'.$this->_lineHeight * $key.'"></rect>';
            $output .= '<svg class="flex-scale-waterfall" width="65%" x="35%">';
            $output .= '<g class="row row-flex">';
            $output .= '<rect height="'.$this->_lineHeight.'" width="100%" x="0" y="'.$this->_lineHeight * $key.'" style="opacity: 0;"></rect>';

            $output .= '<svg width="'.$this->calculateEntryWidth($resData).'" x="'.$this->calculateEntryStart($resData).'">';

            $output .= $this->renderResEntry($resData, $key);

            $output .= '</svg>';

            $output .= '</g>';
            $output .= '</svg>';

            $output .= '<svg class="left-fixed-holder" width="35%" x="0">';
            $output .= '<clipPath id="titleClipPath"><rect height="100%" width="100%"></rect></clipPath>';

            $output .= '<g class="row row-fixed">';

            $output .= '<text x="36.343994140625" y="'.(13 + ($key * $this->_lineHeight)).'" style="text-anchor: end;">'.($key + 1).'</text>';

            $output .= '<text x="40.343994140625" y="'.(13 + ($key * $this->_lineHeight)).'">'.$this->squishUrl($resData['name']).'<title text="'.$url.'"></title></text>';

            $output .= '</g>';

            $output .= '</svg>';

            $output .= '</a>';
        }

        $output .= '</g>';

        return $output;
    }

    /**
     * Render css progress bar.
     *
     * @param int $resourceNumber
     *
     * @return string
     */
    protected function renderResEntry(array $resData, $resourceNumber)
    {
        $renderData = [];

        $renderData['blocking'] = [
            'color' => '#999da3',
            'duration' => ($resData['redirectStart'] > $resData['startTime']) ?
                    $resData['redirectStart'] - $resData['startTime'] : 0,
        ];

        $renderData['redirect'] = [
            'color' => '#ffd942',
            'duration' => $resData['redirectEnd'] - $resData['redirectStart'],
        ];

        $renderData['appCache'] = [
            'color' => '#6cb500',
            'duration' => ($resData['domainLookupStart'] > $resData['fetchStart']) ?
                    $resData['domainLookupStart'] - $resData['fetchStart'] : 0,
        ];

        $renderData['dns'] = [
            'color' => '#159588',
            'duration' => $resData['domainLookupEnd'] - $resData['domainLookupStart'],
        ];

        $renderData['connect'] = [
            'color' => '#fd9727',
            'duration' => ($resData['secureConnectionStart'] > $resData['connectStart']) ?
                    $resData['secureConnectionStart'] - $resData['connectStart'] : $resData['connectEnd'] - $resData['connectStart'],
        ];

        $renderData['secure_connect'] = [
            'color' => '#c141cd',
            'duration' => ($resData['secureConnectionStart'] > $resData['connectStart']) ?
                    $resData['connectEnd'] - $resData['connectStart'] : 0,
        ];

        if (
            0 === $renderData['blocking']['duration']
            && 0 === $renderData['redirect']['duration']
            && 0 === $renderData['dns']['duration']
            && 0 === $renderData['connect']['duration']
            && 0 === $renderData['secure_connect']['duration']
        ) {
            if ($resData['requestStart'] > $resData['fetchStart']) {
                $renderData['blocking']['duration'] = $resData['requestStart'] - $resData['fetchStart'];
            }
        }

        $renderData['request'] = [
            'color' => '#1ec659',
            'duration' => ($resData['responseStart'] > $resData['requestStart']) ?
                    $resData['responseStart'] - $resData['requestStart'] : 0,
        ];

        $renderData['response'] = [
            'color' => '#1eaaf1',
            'duration' => ($resData['responseEnd'] > $resData['responseStart'] && $resData['responseStart'] > 0) ?
                    $resData['responseEnd'] - $resData['responseStart'] : $resData['responseEnd'] - $resData['startTime'],
        ];

        $output = '<g class="rect-holder">';

        $entryLeftDistance = 0;

        foreach ($renderData as $key => $entry) {
            if (0 != $entry['duration'] && 0 != $resData['duration']) {
                $entryWidth = ($entry['duration'] / $resData['duration']) * 100;
            } else {
                /**
                 * The case when duration is exactly 0.
                 */
                $entryWidth = 0.2;
            }

            if ($entryWidth > 0) {
                $output .= '<g><rect fill="'.$entry['color'].'" height="'.$this->_lineHeight.'" x="'.$entryLeftDistance.'%" y="'.$resourceNumber * $this->_lineHeight.'" width="'.$entryWidth.'%"></rect></g>';
            }

            $entryLeftDistance += $entryWidth;
        }

        $output .= '</g>';

        return $output;
    }

    /**
     * @return string
     */
    private function calculateEntryStart(array $resData)
    {
        $width = ($resData['startTime'] / $this->maxLength) * 100;

        return $width.'%';
    }

    /**
     * @return string
     */
    private function calculateEntryWidth(array $resData)
    {
        $duration = $resData['duration'];

        $width = ($duration / $this->maxLength) * 100;

        return $width.'%';
    }

    /**
     * @param int $height
     *
     * @return string
     */
    private function renderTimeLines($height, array $navigationTimings)
    {
        $firstPaint = $navigationTimings['nt_first_paint'] - $navigationTimings['nt_nav_st'];
        $firstByte = $navigationTimings['nt_res_st'] - $navigationTimings['nt_nav_st'];

        $lines = '';

        $lineHorizontalPosition = 0;

        while ($lineHorizontalPosition <= $this->maxLength) {
            $widthPercent = ($lineHorizontalPosition / $this->maxLength) * 100;

            $strokeColor = '#ccc';

            $opacity = '';

            if (0 == $lineHorizontalPosition % 1000) {
                $strokeColor = '#0cc';
            }

            if (0 != $lineHorizontalPosition % 1000) {
                $opacity = 'opacity: 0.75;';
            }

            $lines .= '<line style="stroke: '.$strokeColor.'; stroke-width: 1; '.$opacity.'" x1="'.$widthPercent.'%" x2="'.$widthPercent.'%" y1="0" y2="'.$height.'"></line>';

            if (0 == $lineHorizontalPosition % 1000) {
                $second = $lineHorizontalPosition / 1000;
                $lines .= '<text style="color: #606c71; font-weight: bold;" x="'.$widthPercent.'%" y="-3">'.$second.'s</text>';
            }

            $lineHorizontalPosition += 200;
        }

        if ($firstPaint > 0) {
            $widthPercent = ($firstPaint / $this->maxLength) * 100;
            $lines .= '<line style="stroke: #c141cd; stroke-width: 2;" x1="'.$widthPercent.'%" x2="'.$widthPercent.'%" y1="-25" y2="'.$height.'"></line>';
            $lines .= '<text style="color: #606c71; font-weight: bold;" x="'.($widthPercent - 2).'%" y="-28">First Paint ('.$firstPaint.' ms)</text>';
        }

        if ($firstByte > 0) {
            $widthPercent = ($firstByte / $this->maxLength) * 100;
            $lines .= '<line style="stroke: #1ec659; stroke-width: 2;" x1="'.$widthPercent.'%" x2="'.$widthPercent.'%" y1="-25" y2="'.$height.'"></line>';
            $lines .= '<text style="color: #606c71; font-weight: bold;" x="'.($widthPercent - 2).'%" y="-28">First Byte ('.$firstByte.' ms)</text>';
        }

        $output = '<g><svg width="65%" x="35%"><g>'.$lines.'</g></svg></g>';

        return $output;
    }

    private function squishUrl($url)
    {
        if (\strlen($url) > 40) {
            $frontStr = substr($url, 0, 15);
            $backStr = substr($url, -22);

            return $frontStr.'...'.$backStr;
        }

        return $url;
    }
}
