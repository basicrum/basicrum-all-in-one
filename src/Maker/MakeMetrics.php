<?php

declare(strict_types=1);

namespace App\Maker;

use Doctrine\Common\Annotations\Annotation;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Yaml\Yaml;

final class MakeMetrics extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:basicrum-metrics';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConf)
    {
        $command
            ->setDescription('Creates metrics from predefined config.')
        ;
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $metricsConfig = $this->getMetricsConfig();

        print_r($metricsConfig);
        exit;

        foreach ($metricsConfig as $config) {
            $this->createClass($config, 'BeaconExtract', $io, $generator);
            $this->createClass($config, 'Collaborator', $io, $generator);
            $this->createClass($config, 'ReaderHint', $io, $generator);
            $this->createClass($config, 'WriterHint', $io, $generator);

            $io->note('Created metric: '.$config['metric_name']);
        }

        // Re-generate metrics class map
        $classMapFile = __DIR__.'/../BasicRum/Metrics/MetricsClassMap.php';

        if (file_exists($classMapFile)) {
            unlink($classMapFile);
        }

        $lassNameDetails = $generator->createClassNameDetails(
            'MetricsClassMap',
            'BasicRum\\Metrics\\'
        );

        $classPath = $generator->generateClass(
            $lassNameDetails->getFullName(),
            __DIR__.'/Resources/Metrics/MetricsClassMap.tpl.php',
            ['metrics_config' => $metricsConfig]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
    }

    private function createClass(array $config, $className, ConsoleStyle $io, Generator $generator)
    {
        $rootType = $config['root_type'];
        $subType = $config['sub_type'];
        $folderName = $config['metric_name'];

        // Re-generate metrics
        $classFile = __DIR__.'/../BasicRum/Metrics/'.$rootType.'/'.$subType.'/'.$folderName.'/'.$className.'.php';

        if (file_exists($classFile)) {
            if ('BeaconExtract' === $className) {
                $io->note('Skipping: '.$classFile);

                return;
            }

            $io->note('Regenerating: '.$classFile);
            unlink($classFile);
        }

        $lassNameDetails = $generator->createClassNameDetails(
            $className,
            'BasicRum\\Metrics\\'.$rootType.'\\'.$subType.'\\'.$folderName.'\\'
        );

        $classPath = $generator->generateClass(
            $lassNameDetails->getFullName(),
            __DIR__.'/Resources/Metrics/Sub/'.$className.'.tpl.php',
            $config
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(
            Annotation::class,
            'doctrine/annotations'
        );
    }

    private function getMetricsConfig(): array
    {
        return Yaml::parseFile(__DIR__.'/metrics-config.yaml');
    }
}
