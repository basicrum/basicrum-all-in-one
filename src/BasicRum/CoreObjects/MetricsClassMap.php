<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects;

class MetricsClassMap
{
    public function getCollaboratorsClassNames()
    {
        return [
            TechnicalMetrics\ConnectDuration\Collaborator::class,
            TechnicalMetrics\FirstContentfulPaint\Collaborator::class,
            TechnicalMetrics\FirstPaint\Collaborator::class,
            TechnicalMetrics\LoadEventEnd\Collaborator::class,
            TechnicalMetrics\RedirectsCount\Collaborator::class,
            TechnicalMetrics\TimeToFirstByte\Collaborator::class,
            TechnicalMetrics\DnsDuration\Collaborator::class,
            TechnicalMetrics\RedirectDuration\Collaborator::class,
            TechnicalMetrics\SessionId\Collaborator::class,
        ];
    }
}
