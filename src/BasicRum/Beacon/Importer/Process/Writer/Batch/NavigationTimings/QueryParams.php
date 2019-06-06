<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Writer\Batch\NavigationTimings;

use \App\Entity\NavigationTimingsQueryParams;

class QueryParams
{

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    /**
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $registry
     */
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param array $batch
     * @param int $lastPageViewId
     */
    public function batchInsert(array $batch, int $lastPageViewId)
    {
        $lastPageViewIdStartOffset = $lastPageViewId + 1;

        $mustFlush = false;

        foreach ($batch as $key => $row) {
            if (!empty($row['query_params'])) {
                $mustFlush = true;

                $pageViewId = $key + $lastPageViewIdStartOffset;

                $queryParams = new NavigationTimingsQueryParams();
                $queryParams->setPageViewId($pageViewId);
                $queryParams->setQueryParams($row['query_params']);

                $this->registry->getManager()->persist($queryParams);
            }
        }

        if ($mustFlush) {
            $this->registry->getManager()->flush();
            $this->registry->getManager()->clear();
        }
    }

}