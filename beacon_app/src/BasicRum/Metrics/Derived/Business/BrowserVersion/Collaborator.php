<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Derived\Business\BrowserVersion;

class Collaborator
{
    const ID = 'derived_bm_browser_version';

    const TYPE = 'business';

    const GROUP = 'browser';

    const DB_COLUMN_TYPE = "String";

    const DB_COLUMN_NAME = "browser_version";

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
        return true;
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

    public function getGroup(): string
    {
        return self::GROUP;
    }

    public function derivedFromMetricOnImport(): string
    {
        return "user_agent";
    }

    public function derivedDataExtract(): DerivedDataExtract
    {
        return new DerivedDataExtract();
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
