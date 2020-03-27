<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Plan;

class Select
{
    /** @var \App\BasicRum\Layers\DataLayer\Query\SelectInterface $select */
    private $select;

    /**
     * @param Select $select
     */
    public function __construct(\App\BasicRum\Layers\DataLayer\Query\SelectInterface $select)
    {
        $this->select = $select;
    }

    /**
     * @return Select|\App\BasicRum\Layers\DataLayer\Query\SelectInterface
     */
    public function getSelect()
    {
        return $this->select;
    }
}
