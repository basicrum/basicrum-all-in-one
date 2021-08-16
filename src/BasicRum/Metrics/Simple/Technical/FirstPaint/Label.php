<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\FirstPaint;

use App\BasicRum\Metrics\Interfaces\LabelInterface;


class Label implements LabelInterface
{

    public function labelValue(): string
    {
        return "First Paint";
    }

}
