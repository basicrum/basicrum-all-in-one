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
        $plan = new Plan('navigation_timings');

        /**
         * Check for selects that may break select query.
         *
         * E.g we can't have "SELECT page_view_id, COUNT(page_view_id) ..."
         * We can have only   "SELECT COUNT(page_view_id)"
         */
        $addDefaultSelect = true;

        foreach ($this->requirements as $requirement) {
            if ($requirement instanceof \App\BasicRum\Report\CountableInterface) {
                $addDefaultSelect = false;
            }
        }

        if ($addDefaultSelect) {
            $itself = new Select\Itself(
                'navigation_timings',
                'page_view_id'
            );

            $plan->addSelect($itself);

            foreach ($this->requirements as $requirement) {
                if ($requirement instanceof \App\BasicRum\Report\SelectableInterface) {
                    $itself = new Select\Itself(
                        $requirement->getSelectTableName(),
                        $requirement->getSelectDataFieldName()
                    );

                    $plan->addSelect($itself);
                }
            }
        }

        $between = new Condition\Between(
            'navigation_timings',
            'created_at',
            $this->startPeriod,
            $this->endPeriod
        );

        $plan->addLimiterFilter(
            'navigation_timings',
            'page_view_id',
            'navigation_timings',
            $between,
            new Select\Min(
                'navigation_timings',
                'page_view_id'
            ),
            ">="
        );

        $plan->addLimiterFilter(
            'navigation_timings',
            'page_view_id',
            'navigation_timings',
            $between,
            $max = new Select\Max(
                'navigation_timings',
                'page_view_id'
            ),
            "<="
        );

        foreach ($this->requirements as $requirement) {
            if ($requirement instanceof \App\BasicRum\Report\ComplexSelectableInterface) {
                $plan->addComplexSelect(
                    $requirement->getPrimarySelectTableName(),
                    $requirement->getPrimaryKeyFieldName(),
                    $requirement->getSecondarySelectTableName(),
                    $requirement->getSecondaryKeyFieldName(),
                    $requirement->getSecondarySelectDataFieldNames()
                );
            }

            if ($requirement instanceof \App\BasicRum\Report\CountableInterface) {
                $itself = new Select\Count(
                    $requirement->getSelectTableName(),
                    $requirement->getSelectDataFieldName()
                );

                $plan->addSelect($itself);
            }

            if ($requirement instanceof \App\BasicRum\Report\PrimaryFilterableInterface) {
                if ($requirement->getCondition() === 'isNot') {
                    $condition = new Condition\NotEquals(
                        $requirement->getPrimaryTableName(),
                        $requirement->getPrimarySearchFieldName(),
                        $requirement->getSearchValue()
                    );
                } else {
                    $condition = new Condition\Equals(
                        $requirement->getPrimaryTableName(),
                        $requirement->getPrimarySearchFieldName(),
                        $requirement->getSearchValue()
                    );
                }

                $plan->addPrimaryFilter(
                    $requirement->getPrimaryTableName(),
                    $requirement->getPrimarySearchFieldName(),
                    $condition
                );
            }

            if ($requirement instanceof \App\BasicRum\Report\SecondaryFilterableInterface) {
                $itself = new Select\Itself(
                    $requirement->getSecondaryTableName(),
                    $requirement->getSecondaryKeyFieldName()
                );

                if ($requirement->getCondition() === 'contains') {
                    $condition = new Condition\Contains(
                        $requirement->getSecondaryTableName(),
                        $requirement->getSecondarySearchFieldName(),
                        $requirement->getSearchValue()
                    );
                } else {
                    $condition = new Condition\Equals(
                        $requirement->getSecondaryTableName(),
                        $requirement->getSecondarySearchFieldName(),
                        $requirement->getSearchValue()
                    );
                }

                $plan->addSecondaryFilter(
                    $requirement->getPrimaryTableName(),
                    $requirement->getPrimarySearchFieldName(),
                    $requirement->getSecondaryTableName(),
                    $condition,
                    $itself,
                    $requirement->getCondition()
                );
            }
        }

        return $plan;
    }

}