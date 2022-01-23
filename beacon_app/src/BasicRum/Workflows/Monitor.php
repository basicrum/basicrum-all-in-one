<?php

declare(strict_types=1);

namespace App\BasicRum\Workflows;

class Monitor
{

    private string $userObject;

    private array $markerGroups = [];

    public function __construct(string $userObject)
    {
        $this->userObject = $userObject;
    }

    public function addMarker(string $group, string $key, string $valueDescriptor, string $value) : void
    {
        if (!isset($this->markerGroups[$group])) {
            $this->markerGroups[$group] = [];
        }

        $this->markerGroups[$group][$key] = [
            "value" => $value,
            "valueDescriptor" => $valueDescriptor,
            "timestamp" => (string) intval(microtime(true) * 1000)
        ];
    }

    public function getMarkersByGroups() : array
    {
        return [$this->userObject => $this->markerGroups];
    }

}
