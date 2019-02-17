<?php

declare(strict_types=1);

namespace App\BasicRum;

use App\BasicRum\Layers\DataLayer;

class DiagramOrchestrator
{

    /** @var array */
    private $collaboratorsClassMap = [
        Filters\Collaborator::class,
        TechnicalMetrics\Collaborator::class,
//        Visualize\Collaborator::class,
        Periods\Collaborator::class,
//        Decorators\Collaborator::class,
//        BusinessMetrics\Collaborator::class
    ];

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var array */
    private $collaborators = [];

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry = $registry;

        foreach ($this->collaboratorsClassMap as $class) {
            /** @var CollaboratorsInterface $collaborator */
            $collaborator = new $class();
            $this->collaborators[$collaborator->getCommandParameterName()] = $collaborator;
        }
    }

    // What about return scenario
    public function fillRequirements(array $requirements)
    {
        foreach ($requirements as $requirementCode => $requirement) {
            $this->collaborators[$requirementCode]->applyForRequirement($requirement);
        }
    }

    /**
     * @return array
     */
    public function process()
    {
        /**
         *
         * Process sequence:
         *
         *  technical_metrics - select
         *
         *  filters - what to filter
         *
         *  periods - what to filter in period
         *
         *  business_metrics - calculate
         *
         *  visualize   - applies to every diagram
         *  decorators  - applies to every diagram
         *
         */

        $periods = $this->collaborators['periods']->getRequirements();

        $data = [];

        foreach ($periods as $period) {
            $dataLayer = new DataLayer(
                $this->registry,
                $period,
                array_merge(
                    $this->collaborators['filters']->getRequirements(),
                    $this->collaborators['technical_metrics']->getRequirements()
                )
            );
            $data[] = $dataLayer->process();
        }

        return $data;
    }

}