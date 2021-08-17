<?php

namespace  App\Tests\BasicRum\Beacon\Importer\Process\Writer\Batch\NavigationTimings\UserAgent;

use PHPUnit\Framework\TestCase;

class YandexBrowserTest extends TestCase
{

    /**
     * @group import
     */
    public function testYandexBrowser()
    {
        $userAgentString = 'Mozilla/5.0 (Linux; Android 8.0.0; ATU-L31) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.119 YaBrowser/19.3.4.339.00 Mobile Safari/537.36';

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
                'Enjoy 8e',
                'Huawei',
                'Yandex Browser',
                '19.3',
                'Android',
                '8.0.0'
            ]
        );
    }

}
