<?php

namespace App\BasicRum\Beacon\Importer;

class Process
{

    /** @var  \Symfony\Bridge\Doctrine\RegistryInterface */
    private $registry;

    /**
     * Process constructor.
     * @param \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry = $registry;

        //Avoiding memory leaks
        $this->registry->getManager()->getConnection()->getConfiguration()->setSQLLogger(null);
    }

    public function runImport(Process\Reader\MonolithCatcher $reader, $beaconsCount = 200)
    {
        $batchImporter = new Process\Writer\Batch($this->registry);

        $beacons = $reader->read($beaconsCount);

        $beaconWorker = new Process\Beacon();

        $timings = $beaconWorker->extract($beacons);
        $batchImporter->process($timings);

        return 0;
    }

}