<?php

declare(strict_types=1);

namespace App\BasicRum\DiagramSchema\Filters;

interface CollaboratorsInterface
{
    public function getAllPossibleRequirements(): array;
}
