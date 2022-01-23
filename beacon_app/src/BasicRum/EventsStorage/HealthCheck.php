<?php

declare(strict_types=1);

namespace App\BasicRum\EventsStorage;

class HealthCheck
{

    // Check Config
    // Check if folders are initialised
    // Return results list

    public function check() : array
    {
        return [
            'healthy' => true
        ];
    }

}
