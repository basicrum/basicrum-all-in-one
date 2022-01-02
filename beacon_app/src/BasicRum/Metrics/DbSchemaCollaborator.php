<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics;

class DbSchemaCollaborator
{
    private array $metricsCollaborators;

    private array $collaborators = [];

    public function __construct()
    {
        $classMap = new MetricsClassMap();

        $this->metricsCollaborators = $classMap->getCollaboratorsClassNames();

        $this->spawnCollaborators();
    }

    public function getCollaboratorIds(): array
    {
        return array_keys($this->collaborators);
    }

    private function spawnCollaborators()
    {
        foreach ($this->metricsCollaborators as $class) {
            $collaborator = new $class();
            $this->collaborators[$collaborator->getId()] = $collaborator;
        }
    }

    public function getDbColumnsInfo(): array
    {
        $columns = [];

        foreach ($this->collaborators as $collaborator) {
            $columnName = $collaborator->getDbColumnName();

            $columns[$columnName] = [
                "name" => $columnName,
                "type" => $collaborator->getDbColumnType()
            ];
        }

        return $columns;
    }

}
