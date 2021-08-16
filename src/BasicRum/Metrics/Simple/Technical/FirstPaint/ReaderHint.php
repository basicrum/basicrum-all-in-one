<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\FirstPaint;

use App\BasicRum\Metrics\Interfaces\ReaderHintInterface;

class ReaderHint implements ReaderHintInterface
{
    public function getFieldName(): string
    {
        return 'first_paint';
    }

    public function getTabledName(): string
    {
        return 'rum_data_flat';
    }
}
