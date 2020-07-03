<?php

declare(strict_types=1);

namespace App\Maker;

use \Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Doctrine\Common\Annotations\Annotation;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;


final class MakeMetrics extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:basicrum-metrics';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConf)
    {
        $command
            ->setDescription('Created metrics from predefined config.')
        ;
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $metricNames = ['metric_names' => []];

        foreach ($this->getMetricsConfig() as $config) {

            $metricNames['metric_names'][] = $config['metric_name'];

            $this->createClass($config, 'BeaconExtract', $io, $generator);
            $this->createClass($config, 'Collaborator', $io, $generator);
            $this->createClass($config, 'ReaderHint', $io, $generator);
            $this->createClass($config, 'WriterHint', $io, $generator);

            $io->note('Created metric: ' . $config['metric_name']);
        }

        // Re-generate metrics class map
        $classMapFile = __DIR__ . '/../BasicRum/CoreObjects/MetricsClassMap.php';

        if (file_exists($classMapFile)) {
            unlink($classMapFile);
        }

        $lassNameDetails = $generator->createClassNameDetails(
            'MetricsClassMap',
            'BasicRum\\CoreObjects\\'
        );

        $classPath = $generator->generateClass(
            $lassNameDetails->getFullName(),
            __DIR__ .'/Resources/Metrics/MetricsClassMap.tpl.php',
            $metricNames
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
    }

    private function createClass(array $config, $className, ConsoleStyle $io, Generator $generator)
    {
        $folderName = $config['metric_name'];

        // Re-generate metrics
        $classFile = __DIR__ . '/../BasicRum/CoreObjects/TechnicalMetrics/' . $folderName . '/' . $className . '.php';

        var_dump($classFile);

        if (file_exists($classFile)) {
            var_dump($className);

            if ('BeaconExtract' === $className) {
                $io->note('Skipping: ' . $classFile);
                return;
            }

            $io->note('Regenerating: ' . $classFile);
            unlink($classFile);
        }

        $lassNameDetails = $generator->createClassNameDetails(
            $className,
            'BasicRum\\CoreObjects\\TechnicalMetrics\\' . $folderName . '\\'
        );

        $classPath = $generator->generateClass(
            $lassNameDetails->getFullName(),
            __DIR__ .'/Resources/Metrics/Sub/' . $className . '.tpl.php',
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

    private function getMetricsConfig() : array
    {
        // @todo: wrap this in a YAML config
        return [
            [
                'internal_identifier' => 'tm_connect_duration',
                'metric_name'         => 'ConnectDuration',
                'field_name'          => 'connect_duration',
                'table_name'          => 'rum_data_flat'
            ],
            [
                'internal_identifier' => 'tm_first_contentful_paint',
                'metric_name'         => 'FirstContentfulPaint',
                'field_name'          => 'first_contentful_paint',
                'table_name'          => 'rum_data_flat'
            ],
            [
                'internal_identifier' => 'tm_first_paint',
                'metric_name'         => 'FirstPaint',
                'field_name'          => 'first_paint',
                'table_name'          => 'rum_data_flat'
            ],
            [
                'internal_identifier' => 'tm_load_event_end',
                'metric_name'         => 'LoadEventEnd',
                'field_name'          => 'load_event_end',
                'table_name'          => 'rum_data_flat'
            ],
            [
                'internal_identifier' => 'tm_redirects_count',
                'metric_name'         => 'RedirectsCount',
                'field_name'          => 'redirects_count',
                'table_name'          => 'rum_data_flat'
            ],
            [
                'internal_identifier' => 'tm_time_to_first_byte',
                'metric_name'         => 'TimeToFirstByte',
                'field_name'          => 'first_byte',
                'table_name'          => 'rum_data_flat'
            ],
        ];
    }
}
