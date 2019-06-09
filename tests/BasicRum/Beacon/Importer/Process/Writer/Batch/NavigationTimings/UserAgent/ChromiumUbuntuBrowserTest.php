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
                $result->getUserAgent(),
                $result->getDeviceModel(),
                $result->getDeviceManufacturer(),
                $result->getBrowserName(),
                $result->getBrowserVersion(),
                $result->getOsName(),
                $result->getOsVersion()
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