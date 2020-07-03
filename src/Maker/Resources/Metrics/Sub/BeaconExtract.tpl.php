<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use App\BasicRum\CoreObjects\BeaconExtractInterface;

class BeaconExtract implements BeaconExtractInterface
{
    public function extractValue(array $beacon): int
    {
        //@todo: Add implementation here
        throw new \Exception('Missing implementation in file: ' . __FILE__);
    }
}
