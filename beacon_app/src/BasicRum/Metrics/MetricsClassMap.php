<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics;

class MetricsClassMap
{
    public function getCollaboratorsClassNames(): array
    {
        return [
            Simple\Technical\ConnectDuration\Collaborator::class,
            Simple\Technical\FirstContentfulPaint\Collaborator::class,
            Simple\Technical\CumulativeLayoutShift\Collaborator::class,
            Simple\Technical\LargestContentfulPaint\Collaborator::class,
            Simple\Technical\FirstInputDelay\Collaborator::class,
            Simple\Technical\FirstPaint\Collaborator::class,
            Simple\Technical\LoadEventEnd\Collaborator::class,
            Simple\Technical\RedirectsCount\Collaborator::class,
            Simple\Technical\TimeToFirstByte\Collaborator::class,
            Simple\Technical\DnsDuration\Collaborator::class,
            Simple\Technical\RedirectDuration\Collaborator::class,
            Simple\Technical\DownloadTime\Collaborator::class,
            Simple\Business\SessionId\Collaborator::class,
            Simple\Business\SessionLength\Collaborator::class,
            Simple\Business\Url\Collaborator::class,
            Simple\Business\UserAgent\Collaborator::class,
            Simple\Business\RequestType\Collaborator::class,
            Simple\Business\CreatedAt\Collaborator::class,
            Derived\Business\BrowserName\Collaborator::class,
            Derived\Business\BrowserVersion\Collaborator::class,
            Derived\Business\DeviceType\Collaborator::class,
            Derived\Business\DeviceManufacturer\Collaborator::class
        ];
    }

}
