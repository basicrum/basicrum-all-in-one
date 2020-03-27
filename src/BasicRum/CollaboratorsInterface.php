<?php

declare(strict_types=1);

namespace App\BasicRum;

interface CollaboratorsInterface
{
    public function getCommandParameterName(): string;

    public function applyForRequirement(array $requirement): self;

    public function getRequirements(): array;

    public function getAllPossibleRequirementsKeys(): array;
}
