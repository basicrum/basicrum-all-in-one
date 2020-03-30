<?php

declare(strict_types=1);

namespace App\Command;

use App\BasicRum\Stats\LastBlockingResourceCalculator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LastBlockingResourceCalculateCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'basicrum:last-blocking-resource:calculate';

    /** @var \Doctrine\Persistence\ManagerRegistry */
    private $registry;

    public function __construct(\Doctrine\Persistence\ManagerRegistry $registry)
    {
        $this->registry = $registry;

        parent::__construct();
    }

    protected function configure()
    {
        // ...
    }

    /**
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $calculator = new LastBlockingResourceCalculator($this->registry);
        $c = $calculator->calculate();

        echo $c;

        return 0;
    }
}
