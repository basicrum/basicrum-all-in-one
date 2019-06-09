<?php

namespace  App\Tests\BasicRum\Beacon\Importer\Process\Writer\Batch\NavigationTimings\UserAgent;

use PHPUnit\Framework\TestCase;

class FacebookBrowserTest extends TestCase
{

    /**
     * @group import
     */
    public function testFacebookBrowser()
    {
        $userAgentString = 'Mozilla/5.0 (Linux; Android 7.0; RNE-L21 Build/HUAWEIRNE-L21; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/72.0.3626.105 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/211.0.0.43.112;]';

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
                'Honor 9i',
                'Huawei',
                'Facebook',
                '',
                'Android',
                '7.0'
            ]
        );
    }

}