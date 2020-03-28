<?php

declare(strict_types=1);

namespace App\Command;

use App\BasicRum\Cache\Storage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheCleanCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'basicrum:cache:clean';

    protected function configure()
    {
        // ...
    }

    /**
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->clearCache();

        return 0;
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
