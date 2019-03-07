<?php

declare(strict_types=1);

namespace App\BasicRum\Layers;

use App\BasicRum\CollaboratorsAggregator;

use App\Entity\OperatingSystems;

class Presentation
{

    /** @var CollaboratorsAggregator */
    private $collaboratorsAggregator;

    public function __construct()
    {
        $this->collaboratorsAggregator = new \App\BasicRum\CollaboratorsAggregator();
    }

    /**
     * @return array
     */
    public function getTechnicalMetricsSelectValues()
    {
        $metrics = $this->collaboratorsAggregator
            ->getTechnicalMetrics()
            ->getAllPossibleRequirementsKeys();

        $pairs = [];

        foreach ($metrics as $metric) {
            $label = explode('_', $metric);
            $label = array_map('ucfirst', $label);
            $label = implode(' ', $label);

            $pairs[] = [
                'key'   => $metric,
                'label' => $label
            ];
        }

        return $pairs;
    }

    /**
     * @return array
     */
    public function getOperatingSystemSelectValues(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $repository = $registry
            ->getRepository(OperatingSystems::class);

        $query = $repository->createQueryBuilder('r')
            ->getQuery();

        $operatingSystems = $query->getResult();

        $pairs = [];

        /** @var \App\Entity\OperatingSystems $os  */
        foreach ($operatingSystems as $os) {
            $pairs[] = [
                'key'   => $os->getId(),
                'label' => $os->getLabel()
            ];
        }

        return $pairs;
    }

    /**
     * @param array $samples
     * @param array $requirements
     * @return array
     */
    public function generateDiagramData(array $samples, array $requirements)
    {
        return [];
    }

}