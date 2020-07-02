<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\TechnicalMetrics\FirstContentfulPaint;

use App\BasicRum\CoreObjects\ReaderHintInterface;

class ReaderHint implements ReaderHintInterface
{
    public function getFieldName(): string
    {
        return 'first_contentful_paint';
    }

    public function getTabledName(): string
    {
        return 'rum_data_flat';
    }
}
