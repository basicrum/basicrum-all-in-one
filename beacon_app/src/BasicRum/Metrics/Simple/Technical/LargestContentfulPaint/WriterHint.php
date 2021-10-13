<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\LargestContentfulPaint;

use App\BasicRum\Metrics\Interfaces\WriterHintInterface;

class WriterHint implements WriterHintInterface
{
    public function getFieldName(): string
    {
        return 'largest_contentful_paint';
    }

    public function getTabledName(): string
    {
        return 'rum_data_flat';
    }
}
