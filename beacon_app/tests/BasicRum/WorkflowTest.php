<?php

namespace  App\Tests\BasicRum;

use App\BasicRum\Beacon\Catcher\Storage\Archive;
use App\BasicRum\Beacon\Catcher\Storage\Bundle;
use App\BasicRum\DataImporter\Writer;
use App\BasicRum\Db\ClickHouse\Connection;
use PHPUnit\Framework\TestCase;

use App\BasicRum\DataImporter;

class WorkflowTest extends TestCase
{

    public function testSanity()
    {
        $clickHouseConnectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $archiveUtil = new Archive();

        $bundleStorage = new Bundle();
        $bundleInHosts = $bundleStorage->listAvailableBundlesInHosts();

        $importer = new DataImporter(
            new Writer(
                $clickHouseConnectionMock
            )
        );

        $__utestCount = 0;

        foreach ($bundleInHosts as $host => $bundlesPaths) {
            foreach ($bundlesPaths as $file) {

                $dataToImport = json_decode(file_get_contents($file), true);

                if (is_array($dataToImport))
                {


                    $count = $importer->import($host, $dataToImport);

                    $__utestCount += $count;
                }
                // Cleanup/deleting imported bundles
                // $output->writeln('Deleting file: '.$file);
                // unlink($file);

                break;
            }
            break;
        }

        $this->assertEquals(8, $__utestCount);
    }

}
