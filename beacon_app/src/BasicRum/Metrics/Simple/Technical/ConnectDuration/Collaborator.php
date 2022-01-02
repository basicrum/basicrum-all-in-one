<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\ConnectDuration;

class Collaborator
{
    const ID = 'simple_tm_connect_duration';

    const TYPE = 'technical';

    const GROUP = 'perf_metrics';

    const DB_COLUMN_TYPE = "Nullable(UInt16)";

    const DB_COLUMN_NAME = "connect_duration";

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

    public function getGroup(): string
    {
        return self::GROUP;
    }

    public function getLabel(): Label
    {
        return new Label();
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
