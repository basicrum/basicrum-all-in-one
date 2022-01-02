<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Business\SessionId;

class Collaborator
{
    const ID = 'simple_bm_session_id';

    const TYPE = 'business';

    const GROUP = 'visit';

    const DB_COLUMN_TYPE = "FixedString(43)";

    const DB_COLUMN_NAME = "session_id";

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
