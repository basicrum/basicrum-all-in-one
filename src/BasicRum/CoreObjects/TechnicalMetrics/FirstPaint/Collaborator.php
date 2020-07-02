<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\TechnicalMetrics\FirstPaint;

class Collaborator
{
    const ID = 'tm_first_paint';

    public function getId(): string
    {
        return self::ID;
    }

    public function beaconExtract(): BeaconExtract
    {
        return new BeaconExtract();
    }

    public function writerHint(): WriterHint
    {
        return new WriterHint();
    }

    public function readerHint(): ReaderHint
    {
        return new ReaderHint();
    }
}
