<?php

declare(strict_types=1);

namespace App\BasicRum\Beacon\Importer\Process\Writer\Batch;

use \App\BasicRum\Beacon\Importer\Process\Lib\ResourceTimingDecompressor;

class ResourceTimings
{

    private $registry;

    /** @var ResourceTimings\Url */
    private $_resourceTimingsUrlModel;

    /** @var ResourceTimingDecompressor */
    private $_resourceDecompressor;

    /**
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $registry
     */
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $registry)
    {
        $this->registry = $registry;
        $this->_resourceTimingsUrlModel = new ResourceTimings\Url($registry);
        $this->_resourceDecompressor    = new ResourceTimingDecompressor();
    }

    /**
     * @param array $batch
     * @param int $lastPageViewId
     */
    public function batchInsert(array $batch, int $lastPageViewId)
    {
        $lastPageViewIdStartOffset = $lastPageViewId + 1;

        $resourcesBatch = [];
        $batchUrls = [];

        // Basic filtering for rows that have restiming
        foreach ($batch as $key => $row) {
            if (!empty($row['restiming'])) {
                $viewResources = $this->_resourceDecompressor->decompressResources($row['restiming']);

                // Filling URLs list for batch
                foreach ($viewResources as $resource) {
                    $batchUrls[$resource['name']] = 1;
                }

                $resourcesBatch[$key] = $viewResources;
            }
        }

        $batchUrlsParis = $this->_prepareUrlIds($batchUrls);

        $mustFlush = false;

        foreach ($resourcesBatch as $key => $viewResources) {
            $mustFlush = true;

            $pageViewId = $key + $lastPageViewIdStartOffset;

            //var_dump($pageViewId);

            $startTime = 0;

            $tmingsData = $viewResources;

            // Sort by starting time
            usort($tmingsData, function($a, $b) {
                return $a['startTime'] - $b['startTime'];
            });

            $resources = [];

            foreach ($tmingsData as $timingData) {

                $insertData = [
                    'url_id' => $batchUrlsParis[$timingData['name']]
                ];

                if ($timingData['startTime'] === 0 ) {
                    $insertData['start'] = '';
                } else {
                    $offset = $timingData['startTime'] - $startTime;
                    if ($offset > 0) {
                        $insertData['start'] = base_convert($offset, 10, 36);
                    } else {
                        $insertData['start'] = '';
                    }
                }

                if ($timingData['duration'] !== 0 ) {
                    $insertData['end'] = base_convert($timingData['duration'], 10, 36);
                }

                if (!isset($insertData['end']) && $insertData['start'] == '') {
                    unset($insertData['start']);
                }

                $resources[] = implode(',', $insertData);

                $startTime = $timingData['startTime'];
            }

            $resTiming = new \App\Entity\ResourceTimings();
            
            $resTiming->setPageViewId($pageViewId);
            $resTiming->setResourceTimings(implode(';',$resources));

            $this->registry->getManager()->persist($resTiming);
        }

        if ($mustFlush) {
            $this->registry->getManager()->flush();
            $this->registry->getManager()->clear();
        }
    }

    /**
     * @param array $urlsBatch
     * @return array
     */
    private function _prepareUrlIds(array $urlsBatch)
    {
        return $this->_resourceTimingsUrlModel->insertUrls($urlsBatch);
    }

}