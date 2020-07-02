<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects;

class ImportCollaborator
{
    private array $metricsCollaborators = [
        TechnicalMetrics\TimeToFirstByte\Collaborator::class,
        TechnicalMetrics\LoadEventEnd\Collaborator::class,
        TechnicalMetrics\FirstPaint\Collaborator::class,
        TechnicalMetrics\FirstContentfulPaint\Collaborator::class,
    ];

    private array $collaborators = [];

    /* @var WriterHintInterface[] */
    private array $writerHints = [];

    /* @var ReaderHintInterface[] */
    private array $readerHints = [];

    /* @var BeaconExtractInterface[] */
    private array $beaconExtract = [];

    public function __construct()
    {
        $this->spawnCollaborators();
        $this->spawnWriterHints();
        $this->spawnReaderHints();
        $this->spawnBeaconExtract();
    }

    public function getCollaboratorIds(): array
    {
        return array_keys($this->collaborators);
    }

    public function getWriterHint(string $id): WriterHintInterface
    {
        return $this->writerHints[$id];
    }

    public function getReaderHint(string $id): ReaderHintInterface
    {
        return $this->readerHints[$id];
    }

    public function getBeaconExtract(string $id): BeaconExtractInterface
    {
        return $this->beaconExtract[$id];
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
        /* @var $collaborator TechnicalMetrics\TimeToFirstByte\Collaborator */
        foreach ($this->collaborators as $collaborator) {
            $this->writerHints[$collaborator->getId()] = $collaborator->writerHint();
        }

        return $this;
    }

    private function spawnReaderHints(): self
    {
        /* @var $collaborator TechnicalMetrics\TimeToFirstByte\Collaborator */
        foreach ($this->collaborators as $collaborator) {
            $this->readerHints[$collaborator->getId()] = $collaborator->readerHint();
        }

        return $this;
    }

    private function spawnBeaconExtract(): self
    {
        /* @var $collaborator TechnicalMetrics\TimeToFirstByte\Collaborator */
        foreach ($this->collaborators as $collaborator) {
            $this->beaconExtract[$collaborator->getId()] = $collaborator->beaconExtract();
        }

        return $this;
    }
}
