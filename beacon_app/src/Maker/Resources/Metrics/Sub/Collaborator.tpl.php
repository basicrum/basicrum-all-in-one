<?= "<?php\n"; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

class Collaborator
{
    const ID = '<?= $internal_identifier; ?>';

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
