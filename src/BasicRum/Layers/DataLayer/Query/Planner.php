<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query;

class Planner
{
    /** @var string */
    private $startPeriod;

    /** @var string */
    private $endPeriod;

    /** @var \App\BasicRum\Layers\DataLayer\Query\MainDataSelect\MainDataInterface */
    private $mainDataSelect;

    /** @var array */
    private $requirements = [];

    public function __construct(
        string $startPeriod,
        string $endPeriod,
        array $requirements,
        MainDataSelect\MainDataInterface $mainDataSelect
    ) {
        $this->startPeriod = $startPeriod;
        $this->endPeriod = $endPeriod;
        $this->requirements = $requirements;
        $this->mainDataSelect = $mainDataSelect;
    }

    /**
     * @return Plan
     */
    public function createPlan()
    {
        $plan = new Plan('rum_data_flat', $this->mainDataSelect);

        /**
         * Check for selects that may break select query.
         *
         * E.g we can't have "SELECT rum_data_id, COUNT(page_view_id) ..."
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
                'rum_data_flat',
                'rum_data_id'
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
            'rum_data_flat',
            'created_at',
            $this->startPeriod,
            $this->endPeriod
        );

        $plan->addLimiterFilter(
            'rum_data_flat',
            'rum_data_id',
            'rum_data_flat',
            $between,
            new Select\Min(
                'rum_data_flat',
                'rum_data_id'
            ),
            '>='
        );

        $plan->addLimiterFilter(
            'rum_data_flat',
            'rum_data_id',
            'rum_data_flat',
            $between,
            $max = new Select\Max(
                'rum_data_flat',
                'rum_data_id'
            ),
            '<='
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
                if ('isNot' === $requirement->getCondition()) {
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

                if ('contains' === $requirement->getCondition()) {
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
