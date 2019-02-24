<?php

declare(strict_types=1);

namespace App\BasicRum\Decorators;

interface DecoratorInterface
{

    public function decorate(array $buckets, array $samples) : array;

    public function isApplicable(array $options) : bool;

}