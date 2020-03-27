<?php

declare(strict_types=1);

namespace App\BasicRum\Periods;

class Collaborator implements \App\BasicRum\CollaboratorsInterface
{
    private $periods;

    public function getCommandParameterName(): string
    {
        return 'periods';
    }

    public function applyForRequirement(array $requirements): \App\BasicRum\CollaboratorsInterface
    {
        foreach ($requirements as $requirement) {
            $period = new Period();
            $period->setPeriod($requirement['from_date'], $requirement['to_date']);

            $this->periods[] = $period;
        }

        return $this;
    }

    public function getRequirements(): array
    {
        return $this->periods;
    }

    public function getAllPossibleRequirementsKeys(): array
    {
        return array_keys([]);
    }
}
