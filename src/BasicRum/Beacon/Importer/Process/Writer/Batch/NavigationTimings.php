<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Writer\Batch;

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
        $mustFlush = $this->_prepareNavigationTimings($batch);

        if ($mustFlush) {
            $this->registry->getManager()->flush();
            $this->registry->getManager()->clear();
        }
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
            unset($batch[$key]['restiming']);
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
    private function _prepareNavigationTimings(array $batch)
    {

        $fieldsArr =  array_keys($batch[0]);

        $settersArr = [];

        $mustFlush = false;

        foreach ($fieldsArr as $field) {
            $fieldParts = explode('_', $field);
            $fieldParts = array_map('ucfirst', $fieldParts);
            $settersArr[$field] = 'set' . implode('', $fieldParts);
        }

        foreach ($batch as $row) {
            $mustFlush = true;

            $navigationTiming = new \App\Entity\NavigationTimings();

            foreach ($row as $field => $value) {
                $setter = $settersArr[$field];
                $navigationTiming->$setter($value);
            }

            $this->registry->getManager()->persist($navigationTiming);
        }

        return $mustFlush;
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