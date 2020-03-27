<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Writer\Batch;

use App\BasicRum\Beacon\Importer\Process\Writer\Db\BulkInsertQuery;

class Beacons
{
    private $registry;

    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry = $registry;
    }

    public function batchInsert(array $batch, int $lastPageViewId)
    {
        $lastPageViewIdStartOffset = $lastPageViewId + 1;

        $data = [];

        foreach ($batch as $key => $entry) {
            $pageViewId = $key + $lastPageViewIdStartOffset;

            $data[] = [
                'page_view_id' => $pageViewId,
                'beacon' => $entry['beacon_string'],
            ];
        }

        if (!empty($data)) {
            $bulkInsert = new BulkInsertQuery($this->registry->getConnection(), 'beacons');

            $fieldsArr = array_keys($data[0]);

            $bulkInsert->setColumns($fieldsArr);
            $bulkInsert->setValues($data);
            $bulkInsert->execute();
        }
    }
}
