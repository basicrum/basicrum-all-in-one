<?php

declare(strict_types=1);

namespace App\Command;

use App\BasicRum\Beacon\Catcher\Storage\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BeaconTransferFromRemoteCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'basicrum:beacon:bundle-remote';

    /** @var HttpClientInterface */
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;

        parent::__construct();
    }

    /**
     * @return int
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $catcherEndpoint = $_ENV['CATCHER_ENDPOINT'];
        $catcherEndpoint .= '?origin='.$_ENV['MONITORED_ORIGIN'];

        $response = $this->httpClient->request('GET', $catcherEndpoint);

        if (200 == $response->getStatusCode()) {
            $name = time().'.json';
            $storage = new File();

            $storage->persistBundle($name, $response->getContent());

            $output->writeln('<info>Bundle file: '.$name.'</info>');

            return 0;
        }

        $output->writeln('<error>Problem with fetching beacons</error>');
        $output->writeln('<error>Endpoint responded with code: '.$response->getStatusCode().'</error>');

        return 0;
    }
}
