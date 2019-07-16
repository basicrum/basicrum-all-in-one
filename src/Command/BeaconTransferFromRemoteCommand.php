<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputOption;

class BeaconTransferFromRemoteCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'basicrum:beacons:transfer-from-remote';

    protected function configure()
    {
        $this
            // ...
            ->addOption(
                'json-bundle-path',
                null,
                InputOption::VALUE_REQUIRED,
                'Path to beacons JSON bundle.'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jsonBundlePath = $input->getOption('json-bundle-path');

        $content = file_get_contents($jsonBundlePath);
        $beacons = json_decode($content, true);

        foreach ($beacons as $beacon) {
            $beaconData = json_decode($beacon['beacon_data'], true);
            $beaconData['created_at'] = $beacon['created_at'];

            file_put_contents('/usr/src/app/var/beacons/raw/' . $beacon['id'] . '-' . rand(1, 99999) . '.json', json_encode($beaconData));
        }
    }

}