<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Writer\Batch\NavigationTimings\UserAgent;

use \App\Entity\NavigationTimingsUserAgents;

class Hydrator
{

    /**
     * @param \WhichBrowser\Parser $result
     * @param $userAgentString
     * @return NavigationTimingsUserAgents
     * @throws \Exception
     */
    public function hydrate(\WhichBrowser\Parser $result, $userAgentString)
    {
        $userAgent = new NavigationTimingsUserAgents();

        $userAgent->setUserAgent($userAgentString);
        $userAgent->setDeviceModel($result->device->getModel());
        $userAgent->setDeviceManufacturer($result->device->getManufacturer());
        $userAgent->setBrowserName($result->browser->getName());
        $userAgent->setBrowserVersion($result->browser->getVersion());
        $userAgent->setOsName($result->os->getName());
        $userAgent->setOsVersion($result->os->getVersion());

        return $userAgent;
    }

}