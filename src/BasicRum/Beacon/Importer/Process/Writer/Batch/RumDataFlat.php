<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Writer\Batch;

use App\BasicRum\Beacon\Importer\Process\Writer\Db\BulkInsertQuery;

class RumDataFlat
{
    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var RumData\Url */
    private $_rumDataUrlModel;

    /** @var RumData\UserAgent */
    private $_rumDataUserAgentModel;

    /** @var RumData\QueryParams */
    private $_queryParamsModel;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry = $registry;
        $this->_rumDataUrlModel = new RumData\Url($registry);
        $this->_rumDataUserAgentModel = new RumData\UserAgent($registry);
        $this->_queryParamsModel = new RumData\QueryParams($registry);
    }

    public function batchInsert(array $batch)
    {
        $this->_queryParamsModel->batchInsert($batch, $this->getLastId());
        $batch = $this->_prepareUrlIds($batch);
        $batch = $this->_prepareUserAgentIds($batch);

        $this->_saveRumDataFlat($batch);
    }

    /**
     * @return array
     */
    private function _prepareUrlIds(array $batch)
    {
        $urls = $this->_rumDataUrlModel->insertUrls($batch);

        foreach ($batch as $key => $row) {
            unset($batch[$key]['url']);

            // For testing purposes
            unset($batch[$key]['query_params']);
            unset($batch[$key]['query_params']);
            unset($batch[$key]['beacon_string']);

            $batch[$key]['url_id'] = $urls[$key];
        }

        return $batch;
    }

    /**
     * @return array
     */
    private function _prepareUserAgentIds(array $batch)
    {
        $userAgents = $this->_rumDataUserAgentModel->insertUserAgents($batch);

        foreach ($batch as $key => $row) {
            unset($batch[$key]['user_agent']);

            $batch[$key]['user_agent_id'] = $userAgents[$key]['id'];
            $batch[$key]['device_type_id'] = $userAgents[$key]['device_type_id'];
            $batch[$key]['os_id'] = $userAgents[$key]['os_id'];
        }

        return $batch;
    }

    /**
     * @return bool
     */
    private function _saveRumDataFlat(array $batch)
    {
        $bulkInsert = new BulkInsertQuery($this->registry->getConnection(), 'rum_data_flat');

        $fieldsArr = array_keys($batch[0]);

        $bulkInsert->setColumns($fieldsArr);
        $bulkInsert->setValues($batch);
        $bulkInsert->execute();

        return true;
    }

    /**
     * @return int
     */
    public function getLastId()
    {
        $repository = $this->registry
            ->getRepository(\App\Entity\RumDataFlat::class);

        $queryBuilder = $repository->createQueryBuilder('rdf');

        $queryBuilder->select('max(rdf.rumDataId)');

        $max = $queryBuilder->getQuery()->getSingleScalarResult();

        return !empty($max) ? (int) $max : 0;
    }
}
