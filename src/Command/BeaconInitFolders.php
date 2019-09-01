<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\BasicRum\Beacon\Catcher\Storage\File;

class BeaconInitFolders extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'basicrum:beacon:init-folders';


    protected function configure()
    {
        // ...
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = new File();

        $folders = $file->initFolders();

        foreach ($folders as $folder => $result) {
            if ($result) {
                $output->writeln('<info>Created folder: ' . $folder . '</info>');
            } else {
                $output->writeln('<error>Failed to created folder: ' . $folder. '</error>');
            }
        }
    }

}