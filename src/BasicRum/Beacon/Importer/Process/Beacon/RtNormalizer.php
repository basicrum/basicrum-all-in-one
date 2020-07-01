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

    /**
     * @var array
     */
    private $timing;

    public function normalize(array $timing): array
    {
        $this->timing = $timing;

        // reset *Entries arrays to default values
        $this->resetEntries();

        $this->setIntegerEntries();
        $this->setBooleanEntries();
        $this->setStringEntries();

        return array_merge(
            $this->integerEntries,
            $this->booleanEntries,
            $this->stringEntries,
        );
    }

    public function resetEntries(): void
    {
        foreach ($this->integerEntries as $key => $value) {
            $this->integerEntries[$key] = 0;
        }

        foreach ($this->booleanEntries as $key => $value) {
            $this->booleanEntries[$key] = 0;
        }

        foreach ($this->stringEntries as $key => $value) {
            $this->stringEntries[$key] = null;
        }
    }

    public function setIntegerEntries(): void
    {
        foreach ($this->integerEntries as $key => $value) {
            if (isset($this->timing[$key])) {
                $this->integerEntries[$key] = (int) $this->timing[$key];
            }
        }
    }

    public function setBooleanEntries(): void
    {
        foreach ($this->booleanEntries as $key => $value) {
            if (isset($this->timing[$key])) {
                $this->booleanEntries[$key] = (bool) true;
            }
        }
    }

    public function setStringEntries(): void
    {
        foreach ($this->stringEntries as $key => $value) {
            if (isset($this->timing[$key])) {
                $this->stringEntries[$key] = (string) $this->timing[$key];
            }
        }
    }
}
