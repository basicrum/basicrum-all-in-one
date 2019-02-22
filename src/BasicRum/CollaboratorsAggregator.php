<?php

declare(strict_types=1);

namespace App\BasicRum;

class CollaboratorsAggregator
{

    /** @var array */
    private $collaboratorsClassMap = [
        Filters\Collaborator::class,
        TechnicalMetrics\Collaborator::class,
        Visualize\Collaborator::class,
        Periods\Collaborator::class,
        Decorators\Collaborator::class,
        BusinessMetrics\Collaborator::class
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

    /**
     * @param array $requirements
     * @return CollaboratorsAggregator
     */
    public function fillRequirements(array $requirements) : self
    {
        foreach ($requirements as $requirementCode => $requirement) {
            $this->collaborators[$requirementCode]->applyForRequirement($requirement);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getCollaborators()
    {
        return $this->collaborators;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->collaborators['filters'];
    }

    /**
     * @return array
     */
    public function getTechnicalMetrics()
    {
        return $this->collaborators['technical_metrics'];
    }

    /**
     * @return array
     */
    public function getBusinessMetrics()
    {
        return $this->collaborators['business_metrics'];
    }

}