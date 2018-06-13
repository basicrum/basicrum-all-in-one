<?php

namespace App\BasicRum;

/**
 * PHP port of JS library resourcetiming-compression.js
 *
 * The original JS library can be found here: https://github.com/nicjansma/resourcetiming-compression.js
 *
 * Class ResourceTimingDecompression_v_0_3_4
 */
class ResourceTimingDecompressor_v_0_3_4
{

    /**
     * Initiator type map
     */
    private $INITIATOR_TYPES = [
        "other" => 0,
        "img" => 1,
        "link" => 2,
        "script" => 3,
        "css" => 4,
        "xmlhttprequest" => 5,
        "html" => 6,
        // IMAGE element inside a SVG
        "image" => 7,
        "beacon" => 8,
        "fetch" => 9
    ];

    /**
     * Dimension name map
     */
    private $DIMENSION_NAMES = [
        "height" => 0,
        "width" => 1,
        "y" => 2,
        "x" => 3,
        "naturalHeight" => 4,
        "naturalWidth" => 5
    ];

    /**
     * Script mask map
     */
    private $SCRIPT_ATTRIBUTES = [
        "scriptAsync" => 1,
        "scriptDefer" => 2,
        "scriptBody"  => 4
    ];

    private $REV_INITIATOR_TYPES = [];

    private $REV_DIMENSION_NAMES = [];

    private $REV_SCRIPT_ATTRIBUTES = [];


    // Any ResourceTiming data time that starts with this character is not a time,
    // but something else (like dimension data)
    const SPECIAL_DATA_PREFIX = "*";

    // Dimension data special type
    const SPECIAL_DATA_DIMENSION_TYPE = "0";

    private  $SPECIAL_DATA_DIMENSION_PREFIX;

    // Dimension data special type
    const SPECIAL_DATA_SIZE_TYPE = "1";

    // Dimension data special type
    const SPECIAL_DATA_SCRIPT_TYPE = "2";

    public function __construct()
    {
        /**
         * Reverse initiator type map
         */
        $this->REV_INITIATOR_TYPES = $this->getRevMap($this->INITIATOR_TYPES);

        /**
         * Reverse dimension name map
         */
        $this->REV_DIMENSION_NAMES = $this->getRevMap($this->DIMENSION_NAMES);

        /**
         * Reverse script attribute map
         */
        $this->REV_SCRIPT_ATTRIBUTES = $this->getRevMap($this->SCRIPT_ATTRIBUTES);

        $this->SPECIAL_DATA_DIMENSION_PREFIX = self::SPECIAL_DATA_PREFIX . self::SPECIAL_DATA_DIMENSION_TYPE;
    }

    /**
     * Decompresses a compressed ResourceTiming trie
     *
     * @param array $rt ResourceTiming trie
     * @param string $prefix URL prefix for the current node
     *
     * @returns array
     */
    public function decompressResources(array $rt, $prefix = '')
    {
        $resources = [];

        foreach ($rt as $key => $value) {
            $node = $value;
            $nodeKey = $prefix . $key;

            // strip trailing pipe, which is used to designate a node that is a prefix for
            // other nodes but has resTiming data
            if ("|" === substr($nodeKey, -1)) {
                $nodeKey = rtrim($nodeKey, "|");
            }

            if (is_string($node)) {
                // add all occurences
                $timings = explode("|", $node);

                if (0 === count($timings)) {
                    continue;
                }

                // Make sure we reset the dimensions before each new resource.
                $dimensionData = [];

                if ($this->isDimensionData($timings[0])) {
                    $dimensionData = $this->decompressDimension($timings[0]);

                    // Remove the dimension data from our timings array
                    unset($timings[0]);
                }

                // end-node
                foreach ($timings as $resourceData) {

                    if (count($resourceData) > 0 && $resourceData[0] === self::SPECIAL_DATA_PREFIX) {
                        // dimensions or sizes for this resource
                        continue;
                    }

                    // Decode resource and add dimension data to it.
                    $resources[] = $this->addDimension(
                        $this->decodeCompressedResource($resourceData, $nodeKey),
                        $dimensionData
                    );
                }
            } else {
                // continue down
                $nodeResources = $this->decompressResources($node, $nodeKey);

                $resources = array_merge($resources, $nodeResources);
            }
        }

        return $resources;
    }

    /**
     * Added in order to port ">>>" JS operator
     *
     * This is so called "Zero-fill right shift"
     *
     * @param $a
     * @param $b
     * @return int
     */
    private function zerofill($a,$b) {
        if($a>=0) return $a>>$b;
        if($b==0) return (($a>>1)&0x7fffffff)*2+(($a>>$b)&1);
        return ((~$a)>>$b)^(0x7fffffff>>($b-1));
    }

    /**
     * Returns the index of the first value in the array such that it is
     * greater or equal to x.
     * The search is performed using binary search and the array is assumed
     * to be sorted in ascending order.
     *
     * @param array $arr
     * @param {any} x needle
     * @param {function} by transform function (optional)
     *
     * @returns {number} the desired index or arr.length if x is more than all values.
     */
    private  function searchSortedFirst($arr, $x, $by)
    {
        if (!$arr || count($arr) === 0) {
            return -1;
        }

        $ident = function($a) {
            return $a;
        };

        $by = is_callable($by) ? $by : $ident;
        $x = $by($x);
        $min = -1;
        $max = count($arr);

        while ($min < ($max - 1)) {
            $m = $this->zerofill(($min + $max), 1);
            if ($by($arr[$m]) < $x) {
                $min = $m;
            } else {
                $max = $m;
            }
        }

        return $max;
    }

    /**
     * Returns the index of the last value in the array such that is it less
     * than or equal to x.
     * The search is performed using binary search and the array is assumed
     * to be sorted in ascending order.
     *
     * @param {array} arr haystack
     * @param {any} x needle
     * @param {function} by transform function (optional)
     *
     * @returns {number} the desired index or -1 if x is less than all values.
     */
    private function searchSortedLast($arr, $x, $by)
    {
        if (!$arr || count($arr) === 0) {
            return -1;
        }

        $ident = function($a) {
            return $a;
        };

        $by = is_callable($by) ? $by : $ident;
        $x = $by($x);

        $min = -1;
        $max = count($arr);

        while ($min < ($max - 1)) {
            $m = $this->zerofill(($min + $max), 1);
            if ($x < $by($arr[$m])) {
                $max = $m;
            } else {
                $min = $m;
            }
        }

        return $min;
    }

    /**
     * Returns a map with key/value pairs reversed.
     *
     * @param array $origMap Map we want to reverse.
     *
     * @return array New map with reversed mappings.
     */
    private function getRevMap(array $origMap)
    {
        $revMap = [];

        foreach ($origMap as $key => $value ) {
            $revMap[$origMap[$key]] = $key;
        }

        return $revMap;
    }

    /*
     * Checks that the input contains dimension information.
     *
     * @param {string} resourceData The string we want to check.
     *
     * @returns boolean True if resourceData starts with SPECIAL_DATA_DIMENSION_PREFIX, false otherwise.
     */
    private function isDimensionData($resourceData)
    {
        return $resourceData &&
        (substr($resourceData, 0, strlen($this->SPECIAL_DATA_DIMENSION_PREFIX)) === $this->SPECIAL_DATA_DIMENSION_PREFIX);
    }

    /**
     * Extract height, width, y and x from a string.
     *
     * @param {string} resourceData A string containing dimension data.
     *
     * @returns {object} Dimension data with keys defined by DIMENSION_NAMES.
     */
    private function decompressDimension($resourceData)
    {
        $dimensionData = [];

        // If the string does not contain dimension information, do nothing.
        if (!$this->isDimensionData($resourceData)) {
            return $dimensionData;
        }

        // Remove special prefix
        $resourceData = substr($resourceData, strlen($this->SPECIAL_DATA_DIMENSION_PREFIX));

        $dimensions = explode(",", $resourceData);

        // The data should contain at least height/width.
        if (count($dimensions) < 2) {
            return $dimensionData;
        }

        // Base 36 decode and assign to correct keys of dimensionData.
        for ($i = 0; $i < count($dimensions); $i++) {
            if ($dimensions[$i] === "") {
                $dimensionData[$this->REV_DIMENSION_NAMES[$i]] = 0;
            } else {
                $dimensionData[$this->REV_DIMENSION_NAMES[$i]] = (int) base_convert($dimensions[$i], 36, 10);
            }
        }

        return $dimensionData;
    }

    /**
     * Adds dimension data to the given resource.
     *
     * @param {object} resource The resource we want to edit.
     * @param {object} dimensionData The dimension data we want to add.
     *
     * @returns {object} The resource with added dimensions.
     */
    private function addDimension($resource, $dimensionData)
    {
        // If the resource or data are not defined, do nothing.
        if (!$resource || !$dimensionData) {
            return $resource;
        }

        foreach ($this->DIMENSION_NAMES as $key => $value) {
            if (!empty($dimensionData[$key])) {
                $resource[$key] = $dimensionData[$key];
            }
        }

        return $resource;
    }

    /**
     * Compute a list of cells based on the start/end times of the
     * given array of resources.
     * The returned list of cells is sorted in chronological order.
     *
     * @param {array} rts array of resource timings.
     *
     * @returns {array} Array of cells.
     */
    private function getSortedCells($rts)
    {
        // We have exactly 2 events per resource (start and end).
        // var cells = new Array(rts.length * 2);

        $cells = [];
        for ($i = 0; $i < count($rts); $i++) {
            // Ignore resources with duration <= 0
            if ($rts[$i]['responseEnd'] <= $rts[$i]['startTime']) {
                continue;
            }
            // Increment on resource start
            $cells[] = array(
                'ts' => $rts[$i]['startTime'],
                'val' => 1.0
            );

            // Decrement on resource end
            $cells[] = array(
                'ts' => $rts[$i]['responseEnd'],
                'val' => -1.0
            );
        }

        // Sort in chronological order
        // Checked this answer for equivalent function https://stackoverflow.com/questions/18617410/converting-js-sorting-function-to-php
        usort(
            $cells,
            function($x, $y) {
                return $x['ts'] - $y['ts'];
            }
        );

        return $cells;
    }

    /**
     * Add contributions to the array of cells.
     *
     * @param {array} cells array of cells that need contributions.
     *
     * @returns {array} Array of cells with their contributions.
     */
    private function addCellContributions($cells)
    {
        $tot = 0.0;
        $deleteIdx = [];
        $currentSt = $cells[0]['ts'];
        $cellLen = count($cells);

        for ($i = 0; $i < $cellLen; $i++) {
            $c = $cells[$i];
            // The next timestamp is the same.
            // We don't want to have cells of duration 0, so
            // we aggregate them.
            if (($i < ($cellLen - 1)) && ($cells[$i + 1]['ts'] === $c['ts'])) {
                $cells[$i + 1]['val'] += $c['val'];
                $deleteIdx[] = $i;
                continue;
            }

            $incr = $c['val'];
            if ($tot > 0) {
                // divide time delta by number of active resources.
                $c['val'] = ($c['ts'] - $currentSt) / $tot;
            }

            $currentSt = $c['ts'];
            $tot += $incr;
        }

        // Delete timestamps that don't delimit cells.
        for ($i = count($deleteIdx) - 1; $i >= 0; $i--) {
            unset($cells[$deleteIdx[$i]]);
        }

        return $cells;
    }

    /**
     * Sum the contributions of a single resource based on an array of cells.
     *
     * @param {array} cells Array of cells with their contributions.
     * @param {ResourceTiming} rt a single resource timing object.
     *
     * @returns {number} The total contribution for that resource.
     */
    private function sumContributions($cells, $rt) {
        if (!$rt
            || !isset($rt['startTime'])
            || !isset($rt['responseEnd'])) {

            return 0.0;
        }

        $startTime = $rt['startTime'] + 1;
        $responseEnd = $rt['responseEnd'];

        $getTs = function($x) {
            return $x['ts'];
        };

        // Find indices of cells that were affected by our resource.
        $low = $this->searchSortedFirst($cells, ['ts' => $startTime], $getTs);
        $up = $this->searchSortedLast($cells, ['ts' => $responseEnd], $getTs);

        $tot = 0.0;

        // Sum contributions across all those cells
        for ($i = $low; $i <= $up; $i++) {
            $tot += $cells[$i]['val'];
        }

        return $tot;
    }

    /**
     * Adds contribution scores to all resources in the array.
     *
     * @param {array} rts array of resource timings.
     *
     * @returns {array} Array of resource timings with their contributions.
     */
    private function addContribution($rts)
    {
        if (!$rts || count($rts) === 0) {
            return $rts;
        }

        // Get cells in chronological order.
        $cells = $this->getSortedCells($rts);

        // We need at least two cells and they need to begin
        // with a start event. Furthermore, the last timestamp
        // should be > 0.
        if (count($cells) < 2 ||
            $cells[0]['val'] < 1.0 ||
            $cells[count($cells) - 1]['ts'] <= 0
        ) {
            return $rts;
        }

        // Compute each cell's contribution.
        $cells = $this->addCellContributions($cells);

        // Total load time for this batch of resources.
        $loadTime = $cells[count($cells) - 1]['ts'];

        for ($i = 0; $i < count($rts); $i++) {
            // Compute the contribution of each resource.
            // Normalize by total load time.
            $rts[$i]['contribution'] = $this->sumContributions($cells, $rts[$i]) / $loadTime;
        }

        return $rts;
    }

    /**
     * Determines the initiatorType from a lookup
     *
     * @param {number} index Initiator type index
     *
     * @returns {string} initiatorType, or "other" if not known
     */
    private function getInitiatorTypeFromIndex($index)
    {
        if (isset($this->REV_INITIATOR_TYPES[$index])) {
            return $this->REV_INITIATOR_TYPES[$index];
        }

        return "other";
    }

    /**
     * Decodes a compressed ResourceTiming data string
     *
     * @param {string} data Compressed timing data
     * @param {string} url  URL
     *
     * @returns {ResourceTiming} ResourceTiming pseudo-object (containing all of the properties of a
     * ResourceTiming object)
     */
    private function decodeCompressedResource($data, $url)
    {
        if (!$data || !$url) {
            return [];
        }

        $url = $this->reverseHostname($url);
        $initiatorType = (int) $data[0];
        $data = strlen($data) > 1 ? explode(self::SPECIAL_DATA_PREFIX, $data) : [];
        $timings = count($data) > 0 && strlen($data[0]) > 1 ? explode(",", substr($data[0], 1)) : [];
        $sizes = count($data) > 1 ? $data[1] : "";
        $specialData = count($data) > 1 ? $data[1] : "";

        // convert all timings from base36
        for ($i = 0; $i < count($timings); $i++) {
            if ($timings[$i] === "") {
                // startTime being 0
                $timings[$i] = 0;
            } else {
                // de-base36
                $timings[$i] = (int) base_convert($timings[$i], 36, 10);
            }
        }

        // special case timestamps
        $startTime = count($timings) >= 1 ? $timings[0] : 0;

        // fetchStart is either the redirectEnd time, or startTime
        $fetchStart = count($timings) < 10 ?
            $startTime :
            $this->decodeCompressedResourceTimeStamp($timings, 9, $startTime);

        // all others are offset from startTime
        $res = [
            'name' => $url,
            'initiatorType' => $this->getInitiatorTypeFromIndex($initiatorType),
            'startTime' => $startTime,
            'redirectStart' => $this->decodeCompressedResourceTimeStamp($timings, 9, $startTime) > 0 ? $startTime : 0,
            'redirectEnd' => $this->decodeCompressedResourceTimeStamp($timings, 9, $startTime),
            'fetchStart' => $fetchStart,
            'domainLookupStart' => $this->decodeCompressedResourceTimeStamp($timings, 8, $startTime),
            'domainLookupEnd' => $this->decodeCompressedResourceTimeStamp($timings, 7, $startTime),
            'connectStart' => $this->decodeCompressedResourceTimeStamp($timings, 6, $startTime),
            'secureConnectionStart' => $this->decodeCompressedResourceTimeStamp($timings, 5, $startTime),
            'connectEnd' => $this->decodeCompressedResourceTimeStamp($timings, 4, $startTime),
            'requestStart' => $this->decodeCompressedResourceTimeStamp($timings, 3, $startTime),
            'responseStart' => $this->decodeCompressedResourceTimeStamp($timings, 2, $startTime),
            'responseEnd' => $this->decodeCompressedResourceTimeStamp($timings, 1, $startTime)
        ];

        $res['duration'] = $res['responseEnd'] > 0 ? ($res['responseEnd'] - $res['startTime']) : 0;

        // decompress resource size data
        if (strlen($sizes) > 0) {
            $res = $this->decompressSpecialData($specialData, $res);
        }

        return $res;
    }

    /**
     * Decodes a timestamp from a compressed RT array
     *
     * @param {number[]} timings ResourceTiming timings
     * @param {number} idx Index into array
     * @param {number} startTime NavigationTiming The Resource's startTime
     *
     * @returns {number} Timestamp, or 0 if unknown or missing
     */
    private function decodeCompressedResourceTimeStamp($timings, $idx, $startTime)
    {
        if ($timings && count($timings) >= ($idx + 1)) {
            if ($timings[$idx] !== 0) {
                return $timings[$idx] + $startTime;
            }
        }

        return 0;
    }

    /**
     * Decompresses script load type into the specified resource.
     *
     * @param {string} compressed String with a single integer.
     * @param {ResourceTiming} resource ResourceTiming object.
     * @returns {ResourceTiming} ResourceTiming object with decompressed script type.
     */
    private function decompressScriptType($compressed, $resource = [])
    {
        $data = (int) $compressed;

        foreach ($this->SCRIPT_ATTRIBUTES as $key => $value) {
            $resource[$key] = ($data & $this->SCRIPT_ATTRIBUTES[$key]) === $this->SCRIPT_ATTRIBUTES[$key];
        }

        return $resource;
    }

    /**
     * Decompresses size information back into the specified resource
     *
     * @param {string} compressed Compressed string
     * @param {ResourceTiming} resource ResourceTiming bject
     * @returns {ResourceTiming} ResourceTiming object with decompressed sizes
     */
    private function decompressSize($compressed, $resource)
    {
        $split = explode(',', $compressed);

        for ($i = 0; $i < count($split); $i++) {
            if ($split[$i] === "_") {
                // special non-delta value
                $split[$i] = 0;
            } else {
                // fill in missing numbers
                if ($split[$i] === "") {
                    $split[$i] = 0;
                }

                // convert back from Base36
                $split[$i] = (int) base_convert($split[$i], 36, 10);

                if ($i > 0) {
                    // delta against first number
                    $split[$i] += $split[0];
                }
            }
        }

        // fill in missing
        if (count($split) === 1) {
            // transferSize is a delta from encodedSize
            $split[] = $split[0];
        }

        if (count($split) === 2) {
            // decodedSize is a delta from encodedSize
            $split[] = $split[0];
        }

        // re-add attributes to the resource
        $resource['encodedBodySize'] = $split[0];
        $resource['transferSize'] = $split[1];
        $resource['decodedBodySize'] = $split[2];

        return $resource;
    }

    /**
     * Decompresses special data such as resource size or script type into the given resource.
     *
     * @param {string} compressed Compressed string
     * @param {ResourceTiming} resource ResourceTiming object
     * @returns {ResourceTiming} ResourceTiming object with decompressed special data
     */
    private function decompressSpecialData($compressed, $resource)
    {
        if (!$compressed || strlen($compressed) === 0) {
            return $resource;
        }

        $dataType = $compressed[0];

        $compressed = substr($compressed, 1);

        if ($dataType === self::SPECIAL_DATA_SIZE_TYPE) {
            $resource = $this->decompressSize($compressed, $resource);
        } else if ($dataType === self::SPECIAL_DATA_SCRIPT_TYPE) {
            $resource = $this->decompressScriptType($compressed, $resource);
        }

        return $resource;
    }

    /**
     * Reverse the hostname portion of a URL
     *
     * @param {string} url a fully-qualified URL
     * @returns {string} the input URL with the hostname portion reversed, if it can be found
     */
    private function reverseHostname($url)
    {
        $urlParts = parse_url($url);

        return str_replace($urlParts['host'], strrev($urlParts['host']), $url);
    }

}