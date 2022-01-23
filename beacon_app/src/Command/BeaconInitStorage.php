<?php

declare(strict_types=1);

namespace App\Command;

use App\BasicRum\EventsStorage\Storage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BeaconInitStorage extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'basicrum:beacon:init-storage';

    protected function configure()
    {
        // ...
    }

    /**
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $storage = new Storage();

        $directories = $storage->initDirectories();

        foreach ($directories as $dir => $result) {
            if ($result) {
                $output->writeln('<info>Created directory: '.$dir.'</info>');
            } else {
                $output->writeln('<error>Failed to created directory: '.$dir.'</error>');
            }
        }

        return 0;
    }
}
