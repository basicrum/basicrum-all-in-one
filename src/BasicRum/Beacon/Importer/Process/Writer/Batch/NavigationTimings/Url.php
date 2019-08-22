<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Writer\Batch\NavigationTimings;

use App\BasicRum\Beacon\Importer\Process\Writer\Db\BulkInsertQuery;

class Url
{

    /** @var  \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var array */
    private $_urlsPairs = [];

    /** @var int */
    private $_pairsCount = 0;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry = $registry;

        $this->_reloadPairs();

        $this->_pairsCount = count($this->_urlsPairs);
    }


    /**
     * Returns pair ['navigation timing key array key' => 'url id']
     *
     * @param array $data
     *
     * @return array
     */
    public function insertUrls(array $data)
    {
        $pairs = [];

        $insertData = [];

        $createdAt = date("Y-m-d H:i:s");

        foreach ($data as $key => $row) {
            $url = $row['url'];

            if (isset($this->_urlsPairs[$url])) {
                $pairs[$key] = $this->_urlsPairs[$url];
            } else {
                $this->_pairsCount++;

                $insertData[] = [
                    'url' => $url,
                    'created_at' => $createdAt
                ];

                // Speculatively append to current url pairs
                $this->_urlsPairs[$url] = $this->_pairsCount;
                $pairs[$key] = $this->_pairsCount;
            }
        }

        if (!empty($insertData)) {
            $bulkInsert = new BulkInsertQuery($this->registry->getConnection(), 'navigation_timings_urls');

            $fieldsArr =  array_keys($insertData[0]);

            $bulkInsert->setColumns($fieldsArr);
            $bulkInsert->setValues($insertData);
            $bulkInsert->execute();
        }

        return $pairs;
    }

    private function _reloadPairs()
    {
        $repository = $this->registry
            ->getRepository(\App\Entity\NavigationTimingsUrls::class);

        $queryBuilder = $repository->createQueryBuilder('nturl');

        $queryBuilder->select(['nturl.id', 'nturl.url']);

        $data = $queryBuilder->getQuery()->getArrayResult();

        foreach ($data as $row) {
            $this->_urlsPairs[$row['url']] = $row['id'];
        }
    }

}