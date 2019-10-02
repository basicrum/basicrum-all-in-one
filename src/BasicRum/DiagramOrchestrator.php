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

    /** @var array<\App\BasicRum\Layers\DataLayer\Query\MainDataSelect\MainDataInterface> */
    private $dataFlavors;

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
            }

            $this->collaboratorsAggregators[$key] = $this->_initCollaboratorsAggregator($requirements);
            $this->dataFlavors[$key] = $this->_initDataFlavors($requirements);
        }

        $this->registry = $registry;
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
                    $requirements,
                    $this->dataFlavors[$key]
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

    /**
     * @param array $requirements
     * @return \App\BasicRum\Layers\DataLayer\Query\MainDataSelect\MainDataInterface
     * @throws \Exception
     */
    private function _initDataFlavors(array $requirements) : \App\BasicRum\Layers\DataLayer\Query\MainDataSelect\MainDataInterface
    {
        if (isset($requirements['technical_metrics'])) {
            $metricConfig = current($requirements['technical_metrics']);
            $metricFieldName = array_key_first($requirements['technical_metrics']);

            $dataFlavor = $metricConfig['data_flavor'];

            if (isset($dataFlavor['percentile'])) {
                return new Layers\DataLayer\Query\MainDataSelect\Percentile(
                    'navigation_timings',
                    $metricFieldName,
                    (int) $dataFlavor['percentile']
                );
            }

            if (isset($dataFlavor['histogram'])) {
                return new Layers\DataLayer\Query\MainDataSelect\Histogram(
                    'navigation_timings',
                    $metricFieldName,
                    (int) $dataFlavor['histogram']['bucket']
                );
            }

        }

        if (isset($requirements['business_metrics'])) {
            $metricConfig = current($requirements['business_metrics']);

            $dataFlavor = $metricConfig['data_flavor'];

            if (isset($dataFlavor['bounce_rate'])) {
                return new Layers\DataLayer\Query\MainDataSelect\BounceRateInMetric(
                    'navigation_timings',
                    $dataFlavor['bounce_rate']['in_metric'],
                    200
                );
            }
        }
    }

}