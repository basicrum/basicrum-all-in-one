<?php

declare(strict_types=1);

namespace App\BasicRum\Visit\Data;

use App\Entity\DeviceTypes;

class Filter
{

    /** @var  \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return int
     */
    public function getBotDeviceTypeId() : int
    {
        $userAgentRepo = $this->registry
            ->getRepository(DeviceTypes::class);

        $deviceType = $userAgentRepo->findOneBy(['code' => 'bot']);

        return $deviceType->getId();
    }

}