<?php

declare(strict_types=1);

namespace App\Command;

use App\BasicRum\Visit\Calculator;
use App\BasicRum\Visit\Data\Persist;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VisitGenerateCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'basicrum:visit:generate';

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
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $calculator = new Calculator($this->registry);
        $visits = $calculator->calculate();

        $persist = new Persist($this->registry);
        $persist->saveVisits($visits);

        return 0;
    }
}
