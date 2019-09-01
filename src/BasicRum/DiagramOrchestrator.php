<?php

declare(strict_types=1);

namespace App\BasicRum;

use App\BasicRum\Layers\DataLayer;

use App\BasicRum\Date\TimePeriod;

class DiagramOrchestrator
{

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var array<CollaboratorsAggregator> */
    private $collaboratorsAggregators;

    /**
     * DiagramOrchestrator constructor.
     * @param array $input
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $registry
     * @throws \Exception
     */
    public function __construct(
        array $input,
        \Doctrine\Bundle\DoctrineBundle\Registry $registry
    )
    {
        foreach ($input['segments'] as $key => $segment) {
            $requirements = [];

            if (!empty($input['global']['data_requirements'])) {
                $requirements = $input['global']['data_requirements'];
            }

            if (!empty($segment['data_requirements'])) {
                $requirements = array_merge($requirements, $segment['data_requirements']);

                /**
                 * Temporary hacky way. For now we will group some segments metrics because they
                 * need to know about each other.
                 */
                if (!empty($segment['group_data'])) {
                    $groupRequirements = $this->_getGroupRequirements($input['segments'], $segment['group_data']);
                    $requirements = array_merge($requirements, $groupRequirements);
                }
            }

            $this->collaboratorsAggregators[$key] = $this->_initCollaboratorsAggregator($requirements);
        }

        $this->registry = $registry;
    }

    private function _getGroupRequirements(array $segments, string $group) : array
    {
        $groupRequirements = [];

        foreach ($segments as $key => $segment) {
            if (!empty($segment['group_data'])) {
                if ($segment['group_data'] === $group) {
                    $groupRequirements = array_merge($groupRequirements, $segment['data_requirements']);
                }
            }
        }

        return $groupRequirements;
    }

    /**
     * @return array<CollaboratorsAggregator>
     */
    public function getCollaboratorsAggregator() : array
    {
        return $this->collaboratorsAggregators;
    }

    /**
     * @return array
     */
    public function process()
    {
        $data = [];

        foreach ($this->collaboratorsAggregators as $key => $collaboratorsAggregator) {
            $periods = $collaboratorsAggregator->getPeriods()->getRequirements();

            $requirements = array_merge(
                $collaboratorsAggregator->getFilters()->getRequirements(),
                $collaboratorsAggregator->getTechnicalMetrics()->getRequirements(),
                $collaboratorsAggregator->getBusinessMetrics()->getRequirements()
            );

            foreach ($periods as $period) {
                $dataLayer = new DataLayer(
                    $this->registry,
                    $period,
                    $requirements
                );

                $data[$key] = $dataLayer->process();
            }
        }

        return $data;
    }

    /**
     * @param array $requirements
     * @return CollaboratorsAggregator
     * @throws \Exception
     */
    private function _initCollaboratorsAggregator(array $requirements) : CollaboratorsAggregator
    {
        if (!empty($requirements['period'])) {
            if ('moving' === $requirements['period']['type']) {
                $timePeriod = new TimePeriod();
                $period = $timePeriod->getPastDaysFromNow((int) $requirements['period']['start']);

                $requirements['periods'] = [
                    [
                        'from_date'   => $period->getStart(),
                        'to_date'     => $period->getEnd()
                    ]
                ];
            }

            if ('fixed' === $requirements['period']['type']) {
                $requirements['periods'] = [
                    [
                        'from_date'   => $requirements['period']['start'],
                        'to_date'     => $requirements['period']['end']
                    ]
                ];
            }

            unset($requirements['period']);
        }

        $collaboratorsAggregator = new CollaboratorsAggregator();
        $collaboratorsAggregator->fillRequirements($requirements);

        return $collaboratorsAggregator;
    }

}