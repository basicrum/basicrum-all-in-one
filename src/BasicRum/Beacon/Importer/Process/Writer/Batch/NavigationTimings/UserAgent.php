<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Writer\Batch\NavigationTimings;

use WhichBrowser\Parser;

class UserAgent
{

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var array */
    private $_userAgentsPairs = [];

    /** @var UserAgent\DeviceType */
    private $_deviceTypeModel;

    /** @var UserAgent\OperatingSystem */
    private $_osModel;

    /** @var UserAgent\Hydrator */
    private $_hydrator;

    /** @var int */
    private $_pairsCount = 0;

    /**
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $registry
     */
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry = $registry;

        $this->_reloadPairs();
        $this->_deviceTypeModel = new UserAgent\DeviceType();
        $this->_deviceTypeModel->initDbRecords($registry);

        $this->_osModel = new UserAgent\OperatingSystem($registry);

        $this->_hydrator = new UserAgent\Hydrator();

        $this->_pairsCount = count($this->_userAgentsPairs);
    }

    /**
     * Returns pair ['navigation timing key array key' => 'user agent id']
     *
     * @param array $data
     *
     * @return array
     */
    public function insertUserAgents(array $data)
    {
        $pairs = [];

        $mustFlush = false;

        foreach ($data as $key => $row) {
            $userAgentString = $row['user_agent'];

            if (isset($this->_userAgentsPairs[$userAgentString])) {
                $pairs[$key] = $this->_userAgentsPairs[$userAgentString];
            } else {
                $mustFlush = true;

                $this->_pairsCount++;

                $result = new Parser($userAgentString);

                $userAgent = $this->_hydrator->hydrate($result, $userAgentString);

                $deviceType = !empty($result->device->type) ? $result->device->type : 'unknown';
                $deviceTypeId = $this->_deviceTypeModel->getDeviceTypeIdByCode($deviceType);

                $osId = $this->_osModel->getOsIdByName($result->os->getName());

                $userAgent->setDeviceType($deviceType);
                $userAgent->setDeviceTypeId($deviceTypeId);
                $userAgent->setOsId($osId);
                $userAgent->setCreatedAt(new \DateTime());

                $this->registry->getManager()->persist($userAgent);

                // Speculatively append to current user agent pairs
                $this->_userAgentsPairs[$userAgentString] = [
                    'id'             => $this->_pairsCount,
                    'device_type_id' => $deviceTypeId,
                    'os_id'          => $osId

                ];

                $pairs[$key] = $this->_userAgentsPairs[$userAgentString];
            }
        }

        if ($mustFlush) {
            $this->registry->getManager()->flush();
            $this->registry->getManager()->clear();
        }

        return $pairs;
    }

    private function _reloadPairs()
    {
        $repository = $this->registry
            ->getRepository(\App\Entity\NavigationTimingsUserAgents::class);

        $queryBuilder = $repository->createQueryBuilder('ntua');

        $queryBuilder->select(['ntua.id', 'ntua.userAgent', 'ntua.deviceTypeId', 'ntua.osId']);

        $data = $queryBuilder->getQuery()->getArrayResult();

        foreach ($data as $row) {
            $this->_userAgentsPairs[$row['userAgent']] = [
                'id'             => $row['id'],
                'device_type_id' => $row['deviceTypeId'],
                'os_id'          => $row['osId']
            ];
        }
    }

}