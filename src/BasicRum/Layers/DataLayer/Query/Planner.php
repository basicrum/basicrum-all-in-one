<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query;

use App\BasicRum\Layers\DataLayer\Query\Condition;
use App\BasicRum\Layers\DataLayer\Query\Select;

class Planner
{

    private $startPeriod;

    private $endPeriod;

    /** @var array */
    private $requirements = [];

    public function __construct(
        string $startPeriod,
        string $endPeriod,
        array $requirements
    )
    {
        $this->startPeriod  = $startPeriod;
        $this->endPeriod    = $endPeriod;
        $this->requirements = $requirements;
    }


    /**
     * @return Plan
     */
    public function createPlan()
    {
        $plan = new Plan('NavigationTimings');

        $between = new Condition\Between(
            'NavigationTimings',
            'createdAt',
            $this->startPeriod,
            $this->endPeriod
        );

        $plan->addLimiterFilter(
            'NavigationTimings',
            'pageViewId',
            'NavigationTimings',
            $between,
            new Select\Min(
                'NavigationTimings',
                'pageViewId'
            ),
            ">="
        );

        $plan->addLimiterFilter(
            'NavigationTimings',
            'pageViewId',
            'NavigationTimings',
            $between,
            $max = new Select\Max(
                'NavigationTimings',
                'pageViewId'
            ),
            "<="
        );

        foreach ($this->requirements as $requirement) {
            if ($requirement instanceof \App\BasicRum\Report\ComplexSelectableInterface) {
                $plan->addComplexSelect(
                    $requirement->getPrimarySelectEntityName(),
                    $requirement->getPrimaryKeyFieldName(),
                    $requirement->getSecondarySelectEntityName(),
                    $requirement->getSecondaryKeyFieldName(),
                    $requirement->getSecondarySelectDataFieldNames()
                );
            }

            if ($requirement instanceof \App\BasicRum\Report\SelectableInterface) {
                $plan->addSelect(
                    $requirement->getSelectEntityName(),
                    $requirement->getSelectDataFieldName()
                );
            }

            if ($requirement instanceof \App\BasicRum\Report\PrimaryFilterableInterface) {
                $condition = new Condition\Equals(
                    $requirement->getPrimaryEntityName(),
                    $requirement->getPrimarySearchFieldName(),
                    $requirement->getSearchValue()
                );

                $plan->addPrimaryFilter(
                    $requirement->getPrimaryEntityName(),
                    $requirement->getPrimarySearchFieldName(),
                    $condition
                );
            }

            if ($requirement instanceof \App\BasicRum\Report\SecondaryFilterableInterface) {
                $itself = new Select\Itself(
                    $requirement->getSecondaryEntityName(),
                    $requirement->getSecondaryKeyFieldName()
                );

                if ($requirement->getCondition() === 'contains') {
                    $condition = new Condition\Contains(
                        $requirement->getSecondaryEntityName(),
                        $requirement->getSecondarySearchFieldName(),
                        $requirement->getSearchValue()
                    );
                } else {
                    $condition = new Condition\Equals(
                        $requirement->getSecondaryEntityName(),
                        $requirement->getSecondarySearchFieldName(),
                        $requirement->getSearchValue()
                    );
                }

                $plan->addSecondaryFilter(
                    $requirement->getPrimaryEntityName(),
                    $requirement->getPrimarySearchFieldName(),
                    $requirement->getSecondaryEntityName(),
                    $condition,
                    $itself,
                    "IN"
                );
            }
        }

        return $plan;
    }

}