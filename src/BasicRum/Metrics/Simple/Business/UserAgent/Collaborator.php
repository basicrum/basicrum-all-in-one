<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Business\UserAgent;

class Collaborator
{
    const ID = 'simple_bm_user_agent';

    const TYPE = 'business';

    const GROUP = 'browser';

    public function isDerived(): bool
    {
        return false;
    }

    public function getId(): string
    {
        return self::ID;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getLabel(): Label
    {
        return new Label();
    }

    public function getGroup()
    {
        return self::GROUP;
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

    public function dataFlavorsLink(): DataFlavorsLink
    {
        return new DataFlavorsLink();
    }
}
