<?php

namespace  App\Tests\BasicRum\Beacon\Importer\Process\Writer\Batch\NavigationTimings\UserAgent;

use PHPUnit\Framework\TestCase;

class ChromiumUbuntuBrowserTest extends TestCase
{

    /**
     * @group import
     */
    public function testChromiumUbuntuBrowser()
    {
        $userAgentString = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/74.0.3729.169 Chrome/74.0.3729.169 Safari/537.36';

        $whichBrowserResult = new \WhichBrowser\Parser($userAgentString);

        $hydrator = new \App\BasicRum\Beacon\Importer\Process\Writer\Batch\NavigationTimings\UserAgent\Hydrator();

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
                '',
                '',
                'Chromium',
                '74.0.3729.169',
                'Ubuntu',
                ''
            ]
        );
    }

}