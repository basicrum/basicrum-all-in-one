<?php

declare(strict_types=1);

namespace App\BasicRum;

class DiagramOrchestrator
{

    /** @var array */
    private $collaboratorsClassMap = [
        Filters\Collaborator::class,
//        Visualize\Collaborator::class,
//        TechnicalMetrics\Collaborator::class,
//        Periods\Collaborator::class,
//        Decorators\Collaborator::class,
//        BusinessMetrics\Collaborator::class
    ];

    /** @var array */
    private $collaborators = [];

    public function __construct()
    {
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


    }

}