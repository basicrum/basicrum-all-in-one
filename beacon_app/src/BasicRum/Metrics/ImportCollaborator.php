<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics;

class ImportCollaborator
{
    private array $metricsCollaborators;

    private array $collaborators = [];

    /* @var Interfaces\WriterHintInterface[] */
    private array $writerHints = [];

    public function __construct()
    {
        $classMap = new MetricsClassMap();

        $this->metricsCollaborators = $classMap->getCollaboratorsClassNames();

        $this->spawnCollaborators();
        $this->spawnWriterHints();
    }

    public function getCollaboratorIds(): array
    {
        return array_keys($this->collaborators);
    }

    public function getWriterHint(string $id): Interfaces\WriterHintInterface
    {
        return $this->writerHints[$id];
    }

    private function spawnCollaborators()
    {
        foreach ($this->metricsCollaborators as $class) {
            $collaborator = new $class();
            $this->collaborators[$collaborator->getId()] = $collaborator;
        }
    }

    private function spawnWriterHints(): self
    {
        /* @var $collaborator Simple\Technical\TimeToFirstByte\Collaborator */
        foreach ($this->collaborators as $collaborator) {
            $this->writerHints[$collaborator->getId()] = $collaborator->writerHint();
        }

        return $this;
    }

    public function getBeaconExtractors(): array
    {
        $dataExtractors = [];

        foreach ($this->collaborators as $collaborator) {
            if (!$collaborator->isDerived()) {
                $dataExtractors[$collaborator->getDbColumnName()] = [
                    $collaborator->beaconExtract(),
                    "extractValue"
                ];
            }
        }

        return $dataExtractors;
    }

    public function getDerivedExtractors(): array
    {
        $dataExtractors = [];

        foreach ($this->collaborators as $collaborator) {
            if ($collaborator->isDerived()) {
                if (!isset($dataExtractors[$collaborator->derivedFromMetricOnImport()])) {
                    $dataExtractors[$collaborator->derivedFromMetricOnImport()] = [];
                }

                $dataExtractors[$collaborator->derivedFromMetricOnImport()][$collaborator->getDbColumnName()] = [
                    $collaborator->derivedDataExtract(),
                    "extractValue"
                ];
            }
        }

        return $dataExtractors;
    }
}
