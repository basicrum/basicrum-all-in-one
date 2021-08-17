<?php

namespace  App\Tests\BasicRum\Beacon\Importer\Process\Writer\Batch\NavigationTimings\UserAgent;

use PHPUnit\Framework\TestCase;

class ChromeDevTest extends TestCase
{

    /**
     * @group import
     */
    public function testChromeDev()
    {
        $userAgentString = 'Mozilla/5.0 (Linux; Android 9; SM-G950F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.90 Mobile Safari/537.36';

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
                'Galaxy S8',
                'Samsung',
                'Chrome',
                '73',
                'Android',
                '9'
            ]
        );
    }

}
