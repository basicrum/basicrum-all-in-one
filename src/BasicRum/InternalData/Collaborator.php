<?php

declare(strict_types=1);

namespace App\BasicRum\InternalData;

class Collaborator implements \App\BasicRum\CollaboratorsInterface
{
    /** @var array */
    private $classMap = [
        'data_field' => DataField::class,
    ];

    /** @var array */
    private $collaborators = [];

    public function getCommandParameterName(): string
    {
        return 'internal_data';
    }

    public function applyForRequirement(array $requirements): \App\BasicRum\CollaboratorsInterface
    {
        foreach ($this->classMap as $key => $class) {
            if (isset($requirements[$key])) {
                $decorator = new $class();
                $this->collaborators[$key] = $decorator;
            }
        }

        return $this;
    }

    public function getRequirements(): array
    {
        return $this->collaborators;
    }

    public function getAllPossibleRequirementsKeys(): array
    {
        return array_keys($this->classMap);
    }
}
