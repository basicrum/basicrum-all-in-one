<?php

declare(strict_types=1);

namespace App\BasicRum\Decorators;

class Collaborator implements \App\BasicRum\CollaboratorsInterface
{

    public function getCommandParameterName() : string
    {
        return 'decorators';
    }


    public function applyForRequirement(array $requirement) : \App\BasicRum\CollaboratorsInterface
    {

        return $this;
    }

}