<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Business\SessionLength;

class Collaborator
{
    const ID = 'simple_bm_session_length';

    const TYPE = 'business';

    const GROUP = 'visit';

    const DB_COLUMN_TYPE = "UInt8";

    const DB_COLUMN_NAME = "session_length";

    public function getDbColumnType(): string
    {
        return self::DB_COLUMN_TYPE;
    }

    public function getDbColumnName(): string
    {
        return self::DB_COLUMN_NAME;
    }

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

}
