<?php

declare(strict_types=1);

namespace App\BasicRum;

use App\BasicRum\Layers\DataLayer;

class DiagramOrchestrator
{

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var array */
    private $collaborators = [];

    public function __construct(
        array $collaborators,
        \Doctrine\Bundle\DoctrineBundle\Registry $registry
    )
    {
        $this->collaborators = $collaborators;
        $this->registry      = $registry;
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

        $requirements = array_merge(
            $this->collaborators['filters']->getRequirements(),
            $this->collaborators['technical_metrics']->getRequirements(),
            $this->collaborators['business_metrics']->getRequirements()
        );

        $data = [];

        foreach ($periods as $period) {
            $dataLayer = new DataLayer(
                $this->registry,
                $period,
                $requirements
            );
            $data[] = $dataLayer->process();
        }

        return $data;
    }

}