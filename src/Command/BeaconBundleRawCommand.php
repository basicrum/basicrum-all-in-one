<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\BasicRum\Beacon\Catcher\Storage\File;

class BeaconBundleRawCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'basicrum:beacon:bundle-raw';

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
        $storage = new File();

        $storage->generateBundleFromRawBeacons();
    }

}