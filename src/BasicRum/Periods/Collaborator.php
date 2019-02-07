<?php

declare(strict_types=1);

namespace App\BasicRum\Periods;

class Collaborator implements \App\BasicRum\CollaboratorsInterface
{

    public function getCommandParameterName() : string
    {
        return 'periods';
    }

    public function applyForRequirement(array $requirement) : \App\BasicRum\CollaboratorsInterface
    {

        return $this;
    }

}