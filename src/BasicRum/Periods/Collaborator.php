<?php

declare(strict_types=1);

namespace App\BasicRum\Periods;

class Collaborator implements \App\BasicRum\CollaboratorsInterface
{

    private $periods;

    /**
     * @return string
     */
    public function getCommandParameterName() : string
    {
        return 'periods';
    }

    /**
     * @param array $requirements
     * @return \App\BasicRum\CollaboratorsInterface
     */
    public function applyForRequirement(array $requirements) : \App\BasicRum\CollaboratorsInterface
    {
        foreach ($requirements as $requirement) {
            $period = new Period();
            $period->setPeriod($requirement['from_date'], $requirement['to_date']);

            $this->periods[] = $period;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRequirements() : array
    {
        return $this->periods;
    }

    /**
     * @return array
     */
    public function getAllPossibleRequirementsKeys() : array
    {
        return array_keys([]);
    }

}