<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CacheCleanCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'basicrum:cache-clean';

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
        $cacheAdapter = new FilesystemAdapter('basicrum.report.cache');
        $cacheAdapter->clear();

        $cacheAdapter = new FilesystemAdapter('basicrum.datalayer.runner.cache');
        $cacheAdapter->clear();

        $cacheAdapter = new FilesystemAdapter('basicrum.revenue.estimator.cache');
        $cacheAdapter->clear();
    }

}