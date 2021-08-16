<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Business\CreatedAt;

use App\BasicRum\Metrics\Interfaces\ReaderHintInterface;

class ReaderHint implements ReaderHintInterface
{
    public function getFieldName(): string
    {
        return 'created_at';
    }

    public function getTabledName(): string
    {
        return 'rum_data_flat';
    }
}
