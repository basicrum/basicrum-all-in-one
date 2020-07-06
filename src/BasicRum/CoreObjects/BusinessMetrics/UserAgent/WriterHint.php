<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\BusinessMetrics\UserAgent;

use App\BasicRum\CoreObjects\Interfaces\WriterHintInterface;

class WriterHint implements WriterHintInterface
{
    public function getFieldName(): string
    {
        return 'user_agent';
    }

    public function getTabledName(): string
    {
        return 'rum_data_flat';
    }
}
