<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputOption;

use App\BasicRum\Beacon\Importer\Process;


class BeaconImportFromBeaconCatcherCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'basicrum:beacon:import-from-beacon-catcher';

    /** @var  \Symfony\Bridge\Doctrine\RegistryInterface */
    private $registry;

    public function __construct(\Symfony\Bridge\Doctrine\RegistryInterface $registry)
    {
        $this->registry = $registry;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            // ...
            ->addOption(
                'json-bundle-path',
                null,
                InputOption::VALUE_REQUIRED,
                'Path to beacons JSON bundle.'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jsonBundlePath = $input->getOption('json-bundle-path');

        $reader = new Process\Reader\CatcherService($jsonBundlePath);
        $process = new Process($this->registry);

        echo $process->runImport($reader);
    }

}