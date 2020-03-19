<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Beacon;

class ResourceTimingsNormalizer
{
    /**
     * @param array $resTiming
     * @return array
     */
    public function normalize(array $resTiming)
    {
        $entries = [
            'total_img_size'                => 0,
            'total_js_compressed_size'      => 0,
            'total_js_uncomressed_size'     => 0,
            'total_css_compressed_size'     => 0,
            'total_css_uncomressed_size'    => 0,
            'number_js_files'               => 0,
            'number_css_files'              => 0,
            'number_img_files'              => 0,
        ];

        foreach ($resTiming['restiming'] as $item)
        {
            switch ( $item['initiatorType'] ) {
                // Total Img Size
                case 'img':
                    if ( isset($item['transferSize']) )
                    {
                        $entries['total_img_size']   += $item['transferSize'];
                        $entries['number_img_files'] += 1;
                    }
                    break;
                // Total JS compressed && uncompressed size
                // 3-rd party js scripts don't have encoded/decoded size. So don't count them at all ?
                case 'script':
                    if ( isset($item['encodedBodySize']) && isset($item['decodedBodySize']) )
                    {
                        $entries['total_js_compressed_size']   += $item['encodedBodySize'];
                        $entries['total_js_uncomressed_size']  += $item['decodedBodySize'];
                        $entries['number_js_files']            += 1;
                    }
                    break;
                // Total CSS compressed && uncompressed size
                // Looks like CSS files have encoded/decoded sizes. So count them all
                case 'link':
                    if ( isset($item['encodedBodySize']) && isset($item['decodedBodySize']) )
                    {
                        $entries['total_css_compressed_size']  += $item['encodedBodySize'];
                        $entries['total_css_uncomressed_size'] += $item['decodedBodySize'];
                        $entries['number_css_files']           += 1;
                    }
                    break;
                default:
                    break;
            }

        }

        return $entries;

    }
}