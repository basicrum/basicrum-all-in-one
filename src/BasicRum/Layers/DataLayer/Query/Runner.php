<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query;

class Runner
{

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var array */
    private $planActions = [];

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry, array $planActions)
    {
        $this->registry    = $registry;
        $this->planActions = $planActions;
    }

    /**
     * @return array
     */
    public function run()
    {
        return $this->planActions;
    }

}