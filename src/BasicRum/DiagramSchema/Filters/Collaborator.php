<?php

declare(strict_types=1);

namespace App\BasicRum\DiagramSchema\Filters;

class Collaborator implements \App\BasicRum\DiagramSchema\Filters\CollaboratorsInterface
{
    /** @var array */
    private $filtersClassMap = [
        // 'browser_name' => Secondary\BrowserName::class,
        'device_type' => Primary\DeviceType::class,
        'operating_system' => Primary\OperatingSystem::class,
    ];

    public function getAllPossibleRequirements(): array
    {
        return $this->filtersClassMap;
    }
}
