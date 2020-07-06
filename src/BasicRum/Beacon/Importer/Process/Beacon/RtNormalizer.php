<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Beacon;

class RtNormalizer
{
    /**
     * TODO: Won't implement t_other for now.
     */
    private $integerEntries = [
        't_done' => 0,
        't_page' => 0,
        't_resp' => 0,
        't_load' => 0,
        'rt_tstart' => 0,
        'rt_end' => 0,
        //      't_other' => '',
    ];

    private $booleanEntries = [
        'rt_quit' => 0,
    ];

    private $stringEntries = [
        'http_initiator' => null,
    ];

    public function normalize(array $timing): array
    {
        return array_merge(
            $this->generateIntegerEntries($timing),
            $this->generateBooleanEntries($timing),
            $this->generateStringEntries($timing)
        );
    }

    private function generateIntegerEntries(array $timing): array
    {
        $integerEntries = $this->integerEntries;

        foreach ($integerEntries as $key => $value) {
            if (isset($timing[$key])) {
                $integerEntries[$key] = (int) $timing[$key];
            }
        }

        return $integerEntries;
    }

    public function generateBooleanEntries(array $timing): array
    {
        $booleanEntries = $this->booleanEntries;

        foreach ($booleanEntries as $key => $value) {
            if (isset($timing[$key])) {
                $booleanEntries[$key] = (bool) true;
            }
        }

        return $booleanEntries;
    }

    public function generateStringEntries(array $timing): array
    {
        $stringEntries = $this->stringEntries;

        foreach ($stringEntries as $key => $value) {
            if (isset($timing[$key])) {
                $stringEntries[$key] = (string) $timing[$key];
            }
        }

        return $stringEntries;
    }
}
