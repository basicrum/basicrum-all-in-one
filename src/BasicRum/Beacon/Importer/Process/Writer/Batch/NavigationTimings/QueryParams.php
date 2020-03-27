<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Writer\Batch\NavigationTimings;

use App\BasicRum\Beacon\Importer\Process\Writer\Db\BulkInsertQuery;

class QueryParams
{
    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    private $registry;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry = $registry;
    }

    public function batchInsert(array $batch, int $lastPageViewId)
    {
        $lastPageViewIdStartOffset = $lastPageViewId + 1;

        $insertData = [];

        foreach ($batch as $key => $row) {
            if (!empty($row['query_params'])) {
                $pageViewId = $key + $lastPageViewIdStartOffset;

                $insertData[] = [
                    'page_view_id' => $pageViewId,
                    'query_params' => $row['query_params'],
                ];
            }
        }

        if ($insertData) {
            $bulkInsert = new BulkInsertQuery($this->registry->getConnection(), 'navigation_timings_query_params');

            $fieldsArr = array_keys($insertData[0]);

            $bulkInsert->setColumns($fieldsArr);
            $bulkInsert->setValues($insertData);
            $bulkInsert->execute();
        }
    }
}
