<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\BasicRum\Cache\Storage;

class CacheCleanCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'basicrum:cache:clean';

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
        $this->clearCache();
    }

    public function clearCache()
    {
        $cacheAdapter = new Storage('basicrum.report.cache');
        $cacheAdapter->clear();

        $cacheAdapter = new Storage('basicrum.datalayer.runner.cache');
        $cacheAdapter->clear();

        $cacheAdapter = new Storage('basicrum.revenue.estimator.cache');
        $cacheAdapter->clear();
    }

}