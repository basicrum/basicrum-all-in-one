<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Beacon;

class RtNormalizer
{
    public function normalize(array $timing)
    {
        /**
         * TODO: Do we need t_other? Text field or separated fields?
         */
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

        foreach ($entries as $key => $value) {
            if (isset($timing[$key])) {
                // Need this because rt_quit has no default value when defined
                if ('rt_quit' == $key) {
                    $entries[$key] = 1;
                } else {
                    $entries[$key] = (int) $timing[$key];
                }
            }
        }

        return $entries;
    }
}
