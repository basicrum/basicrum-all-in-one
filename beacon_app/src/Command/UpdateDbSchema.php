<?php

declare(strict_types=1);

namespace App\Command;

use App\BasicRum\Db\ClickHouse\Schema\Migrator;
use App\BasicRum\Metrics\DbSchemaCollaborator;
use App\BasicRum\Db\ClickHouse\Connection;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateDbSchema extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'basicrum:db:update-schema';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Test Connection to ClickHouse
        $config = [
            'host' => getenv('CLICKHOUSE_HOST'),
            'port' => getenv('CLICKHOUSE_PORT'),
            'username' => getenv('CLICKHOUSE_USER'),
            'password' => getenv('CLICKHOUSE_PASS')
        ];

        $migrator = new Migrator(
            new Connection($config),
            new DbSchemaCollaborator()
        );

        $migrator->updateAllTablesSchema();

        return 0;
    }
}
