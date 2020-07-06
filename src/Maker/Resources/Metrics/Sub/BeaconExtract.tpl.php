<?= "<?php\n"; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?php if ('int' === $return_type) : ?>
use App\BasicRum\CoreObjects\Interfaces\BeaconExtractIntInterface;
<?php endif; ?>
<?php if ('string' === $return_type) : ?>
use App\BasicRum\CoreObjects\Interfaces\BeaconExtractStringInterface;
<?php endif; ?>


class BeaconExtract implements BeaconExtract<?= ucfirst($return_type); ?>Interface
{
    public function extractValue(array $beacon): <?php echo $return_type; ?>
    {
        //@todo: Add implementation here
        throw new \Exception('Missing implementation in file: ' . __FILE__);
    }
}
