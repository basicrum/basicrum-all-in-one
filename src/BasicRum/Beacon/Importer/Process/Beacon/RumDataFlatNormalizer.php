<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Beacon;

class RumDataFlatNormalizer
{
    private $fieldsCalculation = [
        'dns_duration' => ['nt_dns_end', 'nt_dns_st'],
        'connect_duration' => ['nt_con_end', 'nt_con_st'],
        //        'ssl'                          => [],
        'first_byte' => ['nt_res_st', 'nt_nav_st'],
        'redirect_duration' => ['nt_red_end', 'nt_red_st'],
        'last_byte_duration' => ['nt_res_end', 'nt_nav_st'],
        'first_paint' => ['nt_first_paint', 'nt_nav_st'],
        'load_event_end' => ['nt_load_end', 'nt_nav_st'],
        'ttfb' => ['nt_res_st', 'nt_req_st'],
        'download_time' => ['nt_res_end', 'nt_req_st'],
        //        'response_duration'            => [],
        //        'document_processing_duration' => [],
        //        'on_load_duration'             => []
    ];

//    private $fieldsPrecalculate = [
//        'pt_fp'  => 1,
//        'pt_fcp' => 1
//    ];

    /**
     * @return array
     */
    public function normalize(array &$rumDataFlat)
    {
        //Rename keys and add specific key convention
        //Fix URL ... remove version
        //Add offsets
        $entries = [];

        foreach ($this->fieldsCalculation as $key => $calculationPair) {
            if (!empty($rumDataFlat[$calculationPair[0]]) && !empty($rumDataFlat[$calculationPair[1]])) {
                $entries[$key] = $rumDataFlat[$calculationPair[0]] - $rumDataFlat[$calculationPair[1]];
            } else {
                $entries[$key] = 0;
            }
        }

        if (!empty($rumDataFlat['pt_fp'])) {
            $entries['first_paint'] = (int) $rumDataFlat['pt_fp'];
        }

        if (!empty($rumDataFlat['pt_fcp'])) {
            $entries['first_contentful_paint'] = (int) $rumDataFlat['pt_fcp'];
        } else {
            $entries['first_contentful_paint'] = 0;
        }

        if (!empty($rumDataFlat['nt_red_cnt'])) {
            $entries['redirects_count'] = (int) $rumDataFlat['nt_red_cnt'];
        } else {
            $entries['redirects_count'] = 0;
        }

        if (!empty($rumDataFlat['u'])) {
            $urlParts = explode('?', $rumDataFlat['u']);

            $entries['url'] = $urlParts[0];

            if (!empty($urlParts[1])) {
                $entries['query_params'] = $urlParts[1];
            }
        }

        if (!empty($rumDataFlat['user_agent'])) {
            $entries['user_agent'] = $rumDataFlat['user_agent'];
        }

        if (!empty($rumDataFlat['pid'])) {
            $entries['process_id'] = $rumDataFlat['pid'];
        }

        if (!empty($rumDataFlat['created_at'])) {
            $entries['created_at'] = $rumDataFlat['created_at'];
        }

        if (empty($rumDataFlat['stay_on_page_time'])) {
            $entries['stay_on_page_time'] = 0;
        }

        if (!empty($rumDataFlat['rt_si'])) {
            $entries['rt_si'] = $rumDataFlat['rt_si'];
        } else {
            $entries['rt_si'] = '';
        }

        //Exceptions
        if ($entries['load_event_end'] < 0) {
            $entries['load_event_end'] = 0;
        }

        if ($entries['load_event_end'] > 65535) {
            $entries['load_event_end'] = 65535;
        }

        if ($entries['first_byte'] < 0) {
            $entries['first_byte'] = 0;
        }

        if ($entries['first_byte'] > 65535) {
            $entries['first_byte'] = 65535;
        }

        if ($entries['last_byte_duration'] < 0) {
            $entries['last_byte_duration'] = 0;
        }

        if ($entries['last_byte_duration'] > 65535) {
            $entries['last_byte_duration'] = 65535;
        }

        if ($entries['first_paint'] > 65535) {
            $entries['first_paint'] = 65535;
        }

        if ($entries['first_paint'] < 0) {
            $entries['first_paint'] = 0;
        }

        if ($entries['connect_duration'] > 65535) {
            $entries['connect_duration'] = 65535;
        }

        if ($entries['connect_duration'] < 0) {
            $entries['connect_duration'] = 0;
        }

        if ($entries['dns_duration'] < 0) {
            $entries['dns_duration'] = 0;
        }

        if ($entries['dns_duration'] > 65535) {
            $entries['dns_duration'] = 65535;
        }

        if ($entries['redirect_duration'] < 0) {
            $entries['redirect_duration'] = 0;
        }

        if ($entries['redirect_duration'] > 65535) {
            $entries['redirect_duration'] = 65535;
        }

        if ($entries['first_contentful_paint'] > 65535) {
            $entries['first_contentful_paint'] = 65535;
        }

        return $entries;
    }
}
