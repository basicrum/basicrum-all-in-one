<?php

declare(strict_types=1);

namespace App\Command;

use App\BasicRum\Db\ClickHouse\Connection;
use App\BasicRum\EventsStorage\Storage;
use App\BasicRum\Workflows\ImportBundles;
use App\BasicRum\Workflows\Monitor;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BeaconImportBundleCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'basicrum:beacon:import-bundle';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $storage = new Storage();

        $config = [
            'host' => '172.17.0.1',
            'port' => '8123',
            'username' => 'default',
            'password' => ''
        ];

        $connection = new Connection($config);

        $monitor = ImportBundles::run($storage, $connection);

        return 0;
    }
}
