<?php

declare(strict_types=1);

namespace App\BasicRum\Layers;

class DataLayer
{

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var \App\BasicRum\CollaboratorsInterface */
    private $period;

    /** @var array */
    private $dataRequirements = [];

    /**
     * @todo: How to make it possible that we do not get Doctrine after passing the object in chain of couple of objects?
     *
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $registry
     * @param \App\BasicRum\CollaboratorsInterface $period
     * @param array $dataRequirements
     */
    public function __construct(
        \Doctrine\Bundle\DoctrineBundle\Registry $registry,
        \App\BasicRum\CollaboratorsInterface $period,
        array $dataRequirements
    )
    {
        $this->registry = $registry;
        $this->period = $period;
        $this->dataRequirements = $dataRequirements;

        echo get_class($this->registry);
    }

}