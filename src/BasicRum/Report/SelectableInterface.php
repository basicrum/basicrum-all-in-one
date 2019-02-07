<?php

declare(strict_types=1);

namespace App\BasicRum\Report;

interface SelectableInterface
{

    public function getDataField() : string;

    public function getEntity() : string;

    public function getRelatedEntity() : string;

    public function getKeyField() : string;

    public function getRelatedKeyField() : string;

}