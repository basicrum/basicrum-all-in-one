<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Writer\Batch\NavigationTimings;

use App\BasicRum\Beacon\Importer\Process\Writer\Db\BulkInsertQuery;
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

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry = $registry;

        $this->_reloadPairs();
        $this->_deviceTypeModel = new UserAgent\DeviceType();
        $this->_deviceTypeModel->initDbRecords($registry);

        $this->_osModel = new UserAgent\OperatingSystem($registry);

        $this->_hydrator = new UserAgent\Hydrator();

        $this->_pairsCount = \count($this->_userAgentsPairs);
    }

    /**
     * Returns pair ['navigation timing key array key' => 'user agent id'].
     *
     * @return array
     */
    public function insertUserAgents(array $data)
    {
        $pairs = [];

        $insertData = [];

        $createdAt = date('Y-m-d H:i:s');

        foreach ($data as $key => $row) {
            $userAgentString = $row['user_agent'];

            if (isset($this->_userAgentsPairs[$userAgentString])) {
                $pairs[$key] = $this->_userAgentsPairs[$userAgentString];
            } else {
                ++$this->_pairsCount;

                $result = new Parser($userAgentString);

                $userAgentData = $this->_hydrator->hydrate($result, $userAgentString);

                $userAgentData['device_type'] = !empty($result->device->type) ? $result->device->type : 'unknown';
                $userAgentData['device_type_id'] = $this->_deviceTypeModel->getDeviceTypeIdByCode($userAgentData['device_type']);

                $osId = $this->_osModel->getOsIdByName($result->os->getName());

                $userAgentData['os_id'] = $osId;
                $userAgentData['created_at'] = $createdAt;

                $insertData[] = $userAgentData;

                // Speculatively append to current user agent pairs
                $this->_userAgentsPairs[$userAgentString] = [
                    'id' => $this->_pairsCount,
                    'device_type_id' => $userAgentData['device_type_id'],
                    'os_id' => $osId,
                ];

                $pairs[$key] = $this->_userAgentsPairs[$userAgentString];
            }
        }

        if (!empty($insertData)) {
            $bulkInsert = new BulkInsertQuery($this->registry->getConnection(), 'navigation_timings_user_agents');

            $fieldsArr = array_keys($insertData[0]);

            $bulkInsert->setColumns($fieldsArr);
            $bulkInsert->setValues($insertData);
            $bulkInsert->execute();
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
                'id' => $row['id'],
                'device_type_id' => $row['deviceTypeId'],
                'os_id' => $row['osId'],
            ];
        }
    }
}
