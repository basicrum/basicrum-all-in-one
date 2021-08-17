<?php

namespace  App\Tests\BasicRum\Beacon\Importer\Process\Writer\Batch\NavigationTimings\UserAgent;

use PHPUnit\Framework\TestCase;

class NoBrowserNameiOsTest extends TestCase
{

    /**
     * @group import
     */
    public function testNoBrowserNameiOs()
    {
        $userAgentString = 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Mobile/13B143';

        $whichBrowserResult = new \WhichBrowser\Parser($userAgentString);

        $hydrator = new \App\BasicRum\Beacon\Importer\Process\Writer\Batch\RumData\UserAgent\Hydrator();

        $result = $hydrator->hydrate($whichBrowserResult, $userAgentString);

        $this->assertEquals(
            [
                $result['user_agent'],
                $result['device_model'],
                $result['device_manufacturer'],
                $result['browser_name'],
                $result['browser_version'],
                $result['os_name'],
                $result['os_version']
            ],
            [
                $userAgentString,
                'iPhone',
                'Apple',
                '',
                '',
                'iOS',
                '9.1'
            ]
        );
    }

}
