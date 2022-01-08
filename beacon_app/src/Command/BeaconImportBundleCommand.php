<?php

declare(strict_types=1);

namespace App\Command;

use App\BasicRum\Beacon\Catcher\Storage\Bundle;
use App\BasicRum\Beacon\Catcher\Storage\Archive;
use App\BasicRum\DataImporter;
use App\BasicRum\Db\ClickHouse\Connection;
use App\BasicRum\DataImporter\Writer;
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
        $archiveUtil = new Archive();

        $bundleStorage = new Bundle();
        $bundleInHosts = $bundleStorage->listAvailableBundlesInHosts();

        // Test Connection to ClickHouse
        $config = [
            'host' => getenv('CLICKHOUSE_HOST'),
            'port' => getenv('CLICKHOUSE_PORT'),
            'username' => getenv('CLICKHOUSE_USER'),
            'password' => getenv('CLICKHOUSE_PASS')
        ];

        $config = [
            'host' => '172.17.0.1',
            'port' => '8123',
            'username' => 'default',
            'password' => ''
        ];

        print_r($config);

        $connection = new Connection($config);

        var_dump($connection->isConnected());

        $importer = new DataImporter(
            new Writer(
                $connection
            )
        );

        foreach ($bundleInHosts as $host => $bundlesPaths) {
            foreach ($bundlesPaths as $file) {

                $dataToImport = json_decode(file_get_contents($file), true);

                if (is_array($dataToImport))
                {

                    $output->writeln('Importing bundle: '.$file);

                    $count = $importer->import($host, $dataToImport);

                    $output->writeln('Beacons imported: '.$count);

                    // $output->writeln('Created archive: '.$archiveUtil->archiveBundles($host, $file));

                }
                // Cleanup/deleting imported bundles
                // $output->writeln('Deleting file: '.$file);
                // unlink($file);

                break;
            }
            break;
        }

        return 0;
    }
}
