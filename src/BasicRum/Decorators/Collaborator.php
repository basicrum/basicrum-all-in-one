<?php

declare(strict_types=1);

namespace App\BasicRum\Decorators;

class Collaborator implements \App\BasicRum\CollaboratorsInterface
{
    /** @var array */
    private $decoratorsClassMap = [
        'density' => Density::class,
    ];

    /** @var array */
    private $decorators = [];

    public function getCommandParameterName(): string
    {
        return 'decorators';
    }

    public function applyForRequirement(array $requirements): \App\BasicRum\CollaboratorsInterface
    {
        foreach ($this->decoratorsClassMap as $filterKey => $class) {
            if (isset($requirements[$filterKey])) {
                $requirement = $requirements[$filterKey];

                if (1 == $requirement) {
                    /** @var DecoratorInterface $decorator */
                    $decorator = new $class();
                    $this->decorators[$filterKey] = $decorator;
                }
            }
        }

        return $this;
    }

    public function getRequirements(): array
    {
        return $this->decorators;
    }

    public function getAllPossibleRequirementsKeys(): array
    {
        return array_keys($this->decoratorsClassMap);
    }
}
