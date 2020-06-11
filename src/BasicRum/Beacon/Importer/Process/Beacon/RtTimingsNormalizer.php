<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Beacon;

class RtTimingsNormalizer
{
    public function normalize(array $timing)
    {
        $entries = [
            't_done' => 0,
            't_page' => 0,
            't_resp' => 0,
            //            't_other' => '',
            't_load' => 0,
            'rt_tstart' => 0,
            'rt_end' => 0,
            'rt_quit' => 0,
        ];

        if (isset($timing) && $timing) {
            if (isset($timing['t_done'])) {
                $entries['t_done'] = $timing['t_done'];
            }
            if (isset($timing['t_page'])) {
                $entries['t_page'] = $timing['t_page'];
            }
            if (isset($timing['t_resp'])) {
                $entries['t_resp'] = $timing['t_resp'];
            }
            // Do we need it? Text field or separated fields?
//                if ($timing['t_other']) {

//                }
            if (isset($timing['t_load'])) {
                $entries['t_load'] = $timing['t_load'];
            }
            if (isset($timing['rt_tstart'])) {
                $entries['rt_tstart'] = $timing['rt_tstart'];
            }
            if (isset($timing['rt_end'])) {
                $entries['rt_end'] = $timing['rt_end'];
            }
            if (isset($timing['rt_quit'])) {
                $entries['rt_quit'] = 1; // boolean
            }
        }

        return $entries;
    }
}
