<?php

declare(strict_types=1);

namespace App\BasicRum\Visit\Calculator;

use App\Entity\DeviceTypes;

class Filter
{

    /** @var  \Symfony\Bridge\Doctrine\RegistryInterface */
    private $registry;

    public function __construct(\Symfony\Bridge\Doctrine\RegistryInterface $registry)
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