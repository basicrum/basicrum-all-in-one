<?php

namespace App\BasicRum\Beacon\Importer;

class Process
{
    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /**
     * Process constructor.
     *
     * @param \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry = $registry;

        //Avoiding memory leaks
        $this->registry->getManager()->getConnection()->getConfiguration()->setSQLLogger(null);
    }

    public function runImport(/**Process\Reader\MonolithCatcher*/ $reader, $batchSize = 300): int
    {
        $batchImporter = new Process\Writer\Batch($this->registry);

        $beacons = $reader->read();

        $beaconWorker = new Process\Beacon();

        $timings = $beaconWorker->extract($beacons);
        $batchImporter->process($timings, $batchSize);

        return \count($beacons);
    }
}
