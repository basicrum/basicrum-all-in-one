<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Beacon;

class NavigationTimingsNormalizer
{

    private $fieldsCalculation = [
        'dns_duration'       => ['nt_dns_end', 'nt_dns_st'],
        'connect_duration'   => ['nt_con_end', 'nt_con_st'],
//        'ssl'                          => [],
        'first_byte'         => ['nt_res_st', 'nt_nav_st'],
        'redirect_duration'  => ['nt_red_end', 'nt_red_st'],
        'last_byte_duration' => ['nt_res_end', 'nt_nav_st'],
        'first_paint'        => ['nt_first_paint', 'nt_nav_st'],
        'load_event_end'     => ['nt_load_end', 'nt_nav_st']
//        'response_duration'            => [],
//        'document_processing_duration' => [],
//        'on_load_duration'             => []
    ];

//    private $fieldsPrecalculate = [
//        'pt_fp'  => 1,
//        'pt_fcp' => 1
//    ];

    /**
     * @param array $navigationTiming
     * @return array
     */
    public function normalize(array &$navigationTiming)
    {
        //Rename keys and add specific key convention
        //Fix URL ... remove version
        //Add offsets
        $entries = [];

        foreach ($this->fieldsCalculation as $key => $calculationPair) {
            if (!empty($navigationTiming[$calculationPair[0]]) && !empty($navigationTiming[$calculationPair[1]])) {
                $entries[$key] = $navigationTiming[$calculationPair[0]] - $navigationTiming[$calculationPair[1]];
            } else {
                $entries[$key] = 0;
            }
        }

        if (!empty($navigationTiming['pt_fp'])) {
            $entries['first_paint'] = (int) $navigationTiming['pt_fp'];
        }

        if (!empty($navigationTiming['pt_fcp'])) {
            $entries['first_contentful_paint'] = (int) $navigationTiming['pt_fcp'];
        } else {
            $entries['first_contentful_paint'] = 0;
        }

        if (!empty($navigationTiming['nt_red_cnt'])) {
            $entries['redirects_count'] = (int) $navigationTiming['nt_red_cnt'];
        } else {
            $entries['redirects_count'] = 0;
        }

        if (!empty($navigationTiming['u'])) {
            $urlParts =  explode("?", $navigationTiming['u']);

            $entries['url'] = $urlParts[0];

            if (!empty($urlParts[1])) {
                $entries['query_params'] = $urlParts[1];
            }
        }

        if (!empty($navigationTiming['user_agent'])) {
            $entries['user_agent'] = $navigationTiming['user_agent'];
        }

        if (!empty($navigationTiming['pid'])) {
            $entries['process_id'] = $navigationTiming['pid'];
        }

        if (!empty($navigationTiming['created_at'])) {
            $entries['created_at'] = $navigationTiming['created_at'];
        }

        if (empty($navigationTiming['stay_on_page_time'])) {
            $entries['stay_on_page_time'] = 0;
        }

        if (!empty($navigationTiming['guid'])) {
            $entries['guid'] = $navigationTiming['guid'];
        } else {
            $entries['guid'] = '';
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