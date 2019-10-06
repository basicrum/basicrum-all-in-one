<?php

declare(strict_types=1);

namespace App\BasicRum\InternalData;

class Collaborator
    implements \App\BasicRum\CollaboratorsInterface
{

    /** @var array */
    private $classMap = [
        'data_field' => DataField::class
    ];

    /** @var array */
    private $collaborators = [];

    /**
     * @return string
     */
    public function getCommandParameterName() : string
    {
        return 'internal_data';
    }

    /**
     * @param array $requirements
     * @return \App\BasicRum\CollaboratorsInterface
     */
    public function applyForRequirement(array $requirements) : \App\BasicRum\CollaboratorsInterface
    {
        foreach ($this->classMap as $key => $class) {
            if (isset($requirements[$key])) {
                $decorator = new $class();
                $this->collaborators[$key] = $decorator;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRequirements() : array
    {
        return $this->collaborators;
    }

    /**
     * @return array
     */
    public function getAllPossibleRequirementsKeys() : array
    {
        return array_keys($this->classMap);
    }

}