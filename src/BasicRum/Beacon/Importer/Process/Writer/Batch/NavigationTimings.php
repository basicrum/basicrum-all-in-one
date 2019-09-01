<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Writer\Batch;

use App\BasicRum\Beacon\Importer\Process\Writer\Db\BulkInsertQuery;

class NavigationTimings
{

    /** @var  \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /** @var NavigationTimings\Url */
    private $_navigationTimingsUrlModel;

    /** @var NavigationTimings\UserAgent */
    private $_navigationTimingsUserAgentModel;

    /** @var NavigationTimings\QueryParams */
    private $_queryParamsModel;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry                         = $registry;
        $this->_navigationTimingsUrlModel       = new NavigationTimings\Url($registry);
        $this->_navigationTimingsUserAgentModel = new NavigationTimings\UserAgent($registry);
        $this->_queryParamsModel                = new NavigationTimings\QueryParams($registry);
    }

    /**
     * @param array $batch
     */
    public function batchInsert(array $batch)
    {
        $this->_queryParamsModel->batchInsert($batch, $this->getLastId());
        $batch = $this->_prepareUrlIds($batch);
        $batch = $this->_prepareUserAgentIds($batch);

        $this->_saveNavigationTimings($batch);
    }

    /**
     * @param array $batch
     * @return array
     */
    private function _prepareUrlIds(array $batch)
    {
        $urls = $this->_navigationTimingsUrlModel->insertUrls($batch);

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
     * @param array $batch
     * @return array
     */
    private function _prepareUserAgentIds(array $batch)
    {
        $userAgents = $this->_navigationTimingsUserAgentModel->insertUserAgents($batch);

        foreach ($batch as $key => $row) {
            unset($batch[$key]['user_agent']);

            $batch[$key]['user_agent_id']  = $userAgents[$key]['id'];
            $batch[$key]['device_type_id'] = $userAgents[$key]['device_type_id'];
            $batch[$key]['os_id']          = $userAgents[$key]['os_id'];
        }

        return $batch;
    }

    /**
     * @param array $batch
     * @return bool
     */
    private function _saveNavigationTimings(array $batch)
    {
        $bulkInsert = new BulkInsertQuery($this->registry->getConnection(), 'navigation_timings');

        $fieldsArr =  array_keys($batch[0]);

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
            ->getRepository(\App\Entity\NavigationTimings::class);

        $queryBuilder = $repository->createQueryBuilder('nt');

        $queryBuilder->select('max(nt.pageViewId)');

        $max = $queryBuilder->getQuery()->getSingleScalarResult();

        return !empty($max) ? (int) $max : 0;
    }

}