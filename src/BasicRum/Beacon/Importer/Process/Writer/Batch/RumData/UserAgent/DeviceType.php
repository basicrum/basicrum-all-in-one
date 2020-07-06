<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Writer\Batch\RumData\UserAgent;

class DeviceType
{
    /** @var array */
    private $deviceCodeInternalIdMap = [
        'mobile' => 1,
        'desktop' => 2,
        'tablet' => 3,
        'bot' => 4,
        'other' => 5,
    ];

    /** @var array */
    private $deviceCodeLabelMap = [
        'mobile' => 'Mobile',
        'desktop' => 'Desktop',
        'tablet' => 'Tablet',
        'bot' => 'Bot',
        'other' => 'Other',
    ];

    public function getDeviceTypeIdByCode(string $code): int
    {
        return isset($this->deviceCodeInternalIdMap[$code]) ?
            $this->deviceCodeInternalIdMap[$code] : $this->deviceCodeInternalIdMap['other'];
    }

    public function initDbRecords(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $repository = $registry
            ->getRepository(\App\Entity\DeviceTypes::class);

        $queryBuilder = $repository->createQueryBuilder('dt');

        $queryBuilder->select('count(dt.id)');

        $count = $queryBuilder->getQuery()->getSingleScalarResult();

        if (0 == $count) {
            foreach ($this->deviceCodeLabelMap as $code => $label) {
                $deviceType = new \App\Entity\DeviceTypes();

                $deviceType->setId($this->getDeviceTypeIdByCode($code));
                $deviceType->setLabel($label);
                $deviceType->setCode($code);

                $registry->getManager()->persist($deviceType);
            }

            $registry->getManager()->flush();
            $registry->getManager()->clear();
        }
    }
}
