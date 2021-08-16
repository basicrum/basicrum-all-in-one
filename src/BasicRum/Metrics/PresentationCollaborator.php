<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics;

class PresentationCollaborator
{
    private array $metricsCollaborators;

    private array $collaborators = [];

    private array $perfMetricsCollaborators = [];

    public function __construct()
    {
        $classMap = new MetricsClassMap();

        $this->metricsCollaborators = $classMap->getCollaboratorsClassNames();

        $this->spawnCollaborators();
        $this->spawnPerfMetricsGroup();
    }

    private function spawnCollaborators()
    {
        foreach ($this->metricsCollaborators as $class) {
            $collaborator = new $class();
            $this->collaborators[$collaborator->getId()] = $collaborator;
        }
    }

    private function spawnPerfMetricsGroup()
    {
        foreach ($this->metricsCollaborators as $class) {
            $collaborator = new $class();

            if ("perf_metrics" === $collaborator->getGroup()) {
                $this->perfMetricsCollaborators[$collaborator->getId()] = $collaborator;
            }
        }
    }

    public function getPerfMetricsGroup(): array
    {
        return $this->perfMetricsCollaborators;
    }
}
