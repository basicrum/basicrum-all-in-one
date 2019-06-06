<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\BasicRum\Beacon\Importer\Process;

class ImportBeaconsCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'basicrum:import-beacons';

    /** @var  \Symfony\Bridge\Doctrine\RegistryInterface */
    private $registry;

    public function __construct(\Symfony\Bridge\Doctrine\RegistryInterface $registry)
    {
        $this->registry = $registry;

        parent::__construct();
    }

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
        $beaconFiles = glob(__DIR__ . '/../../var/beacons/*');

        foreach ($beaconFiles as $filePath) {
            $output->writeln('<info>' . $filePath . '</info>');
            $output->writeln('<info>Memory usage: ' . memory_get_usage() . '</info>');


            $reader = new Process\Reader\MonolithCatcher($filePath);
            $process = new Process($this->registry);
            $c = $process->runImport($reader);
        }

        echo 0;
    }

}