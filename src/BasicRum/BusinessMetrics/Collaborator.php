<?php

declare(strict_types=1);

namespace App\BasicRum\BusinessMetrics;

class Collaborator implements \App\BasicRum\CollaboratorsInterface
{

    public function getCommandParameterName() : string
    {
        return 'business_metrics';
    }

    public function applyForRequirement(array $requirement) : \App\BasicRum\CollaboratorsInterface
    {
        return $this;
    }

    public function getRequirements() : array
    {
        return [];
    }

}