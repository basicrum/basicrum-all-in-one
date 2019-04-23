<?php

declare(strict_types=1);

namespace App\BasicRum\Layers;

use App\BasicRum\CollaboratorsAggregator;

use App\Entity\OperatingSystems;
use App\Entity\PageTypeConfig;

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
    public function getTechnicalMetricsSelectValues() : array
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
     * @param \Doctrine\Common\Persistence\ManagerRegistry $doctrine
     * @return array
     */
    public function getOperatingSystemSelectValues(\Doctrine\Common\Persistence\ManagerRegistry $doctrine) : array
    {
        $repository = $doctrine
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
     * @param \Doctrine\Common\Persistence\ManagerRegistry $doctrine
     * @return array
     */
    public function getPageTypes(\Doctrine\Common\Persistence\ManagerRegistry $doctrine) : array
    {
        $repository = $doctrine
            ->getRepository(PageTypeConfig::class);

        $query = $repository->createQueryBuilder('ptc')
            ->getQuery();

        $query->getResult();

        return $query->getResult();
    }

}