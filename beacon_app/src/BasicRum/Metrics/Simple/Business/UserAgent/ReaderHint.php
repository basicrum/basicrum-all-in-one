<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Business\UserAgent;

use App\BasicRum\Metrics\Interfaces\ReaderHintInterface;

class ReaderHint implements ReaderHintInterface
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
