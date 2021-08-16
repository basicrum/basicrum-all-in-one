<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics;

class DataFetchCollaborator
{
    private array $metricsCollaborators;

    private array $collaborators = [];

    /* @var Interfaces\ReaderHintInterface[] */
    private array $readerHints = [];

    public function __construct()
    {
        $classMap = new MetricsClassMap();

        $this->metricsCollaborators = $classMap->getCollaboratorsClassNames();

        $this->spawnCollaborators();
        $this->spawnReaderHints();
    }

    public function getCollaboratorIds(): array
    {
        return array_keys($this->collaborators);
    }

    public function getReaderHint(string $id): Interfaces\ReaderHintInterface
    {
        return $this->readerHints[$id];
    }

    private function spawnCollaborators()
    {
        foreach ($this->metricsCollaborators as $class) {
            $collaborator = new $class();
            $this->collaborators[$collaborator->getId()] = $collaborator;
        }
    }

    private function spawnReaderHints(): self
    {
        /* @var $collaborator Simple\Technical\TimeToFirstByte\Collaborator */
        foreach ($this->collaborators as $collaborator) {
            $this->readerHints[$collaborator->getId()] = $collaborator->readerHint();
        }

        return $this;
    }

}
