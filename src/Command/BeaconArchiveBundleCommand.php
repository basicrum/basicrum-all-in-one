<?php

declare(strict_types=1);

namespace App\Command;

use App\BasicRum\Beacon\Catcher\Storage\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BeaconArchiveBundleCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'basicrum:beacon:archive-bundle';

    protected function configure()
    {
        // ...
    }

    /**
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = new File();

        $file->archiveBundles();

        return 0;
    }
}
