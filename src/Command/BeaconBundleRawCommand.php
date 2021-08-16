<?php

declare(strict_types=1);

namespace App\Command;

use App\BasicRum\Beacon\Catcher\Storage\Bundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BeaconBundleRawCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'basicrum:beacon:bundle-raw';

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        // ...
    }

    /**
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bundleStorage = new Bundle();

        $bundleStorage->generateBundleFromRawBeacons();

        return 0;
    }
}
