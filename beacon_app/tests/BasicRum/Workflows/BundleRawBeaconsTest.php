<?php

namespace  App\Tests\BasicRum;

use App\BasicRum\EventsStorage\Storage;
use App\BasicRum\Workflows\BundleRawBeacons;

use PHPUnit\Framework\TestCase;

class BundleRawBeaconsTest extends TestCase
{

    public function testSanity()
    {
        $storageMock = $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storageMock
            ->expects($this->exactly(1))
            ->method('listRawBeaconsHosts')
            ->willReturn([
                'testhost1_com',
                'testhost2_com'
            ]);

        $storageMock
            ->expects($this->exactly(2))
            ->method('listRawBeaconsInHost')
            ->withConsecutive(
                ['testhost1_com'],
                ['testhost2_com'],
            )
            ->will($this->returnCallback(array($this, 'returnRawBeaconsInHostCallback')));

        $storageMock
            ->expects($this->exactly(2))
            ->method('createBeaconsBundle')
            ->withConsecutive(
                [
                    'testhost1_com',
                    [
                        'testhost1_com/1.json',
                        'testhost1_com/2.json',
                        'testhost1_com/3.json'
                    ]
                ],
                [
                    'testhost2_com',
                    [
                        'testhost2_com/1.json',
                        'testhost2_com/2.json'
                    ]
                ],
            )
            ->will($this->returnCallback(array($this, 'returnCreateBeaconsBundleCallback')));

        $storageMock
            ->expects($this->exactly(2))
            ->method('deleteRawBeacons')
            ->withConsecutive(
                [
                    [
                        'testhost1_com/1.json',
                        'testhost1_com/2.json',
                        'testhost1_com/3.json'
                    ]
                ],
                [
                    [
                        'testhost2_com/1.json',
                        'testhost2_com/2.json'
                    ]
                ]
            )
            ->will($this->returnCallback(array($this, 'returnDeleteRawBeaconsCallback')));

        $monitor = BundleRawBeacons::run($storageMock);

        $monitored = $monitor->getMarkersByGroups();

        // Check Success
        $this->assertCount(0, $monitored['App\BasicRum\Workflows\BundleRawBeacons']);
    }

    public function returnRawBeaconsInHostCallback(): array
    {
        $args = func_get_args();

        $host = $args[0];

        if ('testhost1_com' === $host) {
            return [
                'testhost1_com/1.json',
                'testhost1_com/2.json',
                'testhost1_com/3.json'
            ];
        }

        if ('testhost2_com' === $host) {
            return [
                'testhost2_com/1.json',
                'testhost2_com/2.json'
            ];
        }

        return [];
    }

    public function returnCreateBeaconsBundleCallback() : array
    {
        $args = func_get_args();

        $host = $args[0];

        if ('testhost1_com' === $host) {
            return ['size' => 15];
        }

        if ('testhost2_com' === $host) {
            return ['size' => 8];
        }

        return [];
    }

    public function returnDeleteRawBeaconsCallback() : array
    {
        $args = func_get_args();

        $beaconsPaths = $args[0];

        if (3 === count($beaconsPaths)) {
            return [
                'testhost1_com/1.json' => true,
                'testhost1_com/2.json' => true,
                'testhost1_com/3.json' => true
            ];
        }

        if (2 === count($beaconsPaths)) {
            return [
                'testhost2_com/1.json' => true,
                'testhost2_com/2.json' => true
            ];
        }

        return [];
    }

}
