<?php

declare(strict_types=1);

namespace App\BasicRum\Stats;

use App\Entity\NavigationTimings;
use App\Entity\ResourceTimings;
use App\Entity\NavigationTimingsUserAgents;
use App\Entity\ResourceTimingsAttributes;

use App\BasicRum\ResourceTiming\Decompressor;


class LastBlockingResourceCalculator
{

    /** @var int */
    private $scannedChunkSize = 1000;

    private $batchSize        = 500;

    /** @var  \Symfony\Bridge\Doctrine\RegistryInterface */
    private $registry;

    public function __construct(\Symfony\Bridge\Doctrine\RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function calculate()
    {
        $lastPageViewId = $this->_getPreviousLastScannedPageViewId();

        $badBeacons = 0;

        $searchNames = [];

        /**
         *
         * // 0 => HEAD, 1 => BODY
         * ResourceTimingCompression.SPECIAL_DATA_SCRIPT_LOCAT_ATTR = 0x4;
         *
         * We may have information if the resource is in head or body tag
         *
         */
        $searchNames['jquery-ui.css'] = 1;
        $searchNames['1.css'] = 1;
        $searchNames['jquery-1.11.0.min.js'] = 1;
        $searchNames['respond.src.js'] = 1;
        $searchNames['script.js'] = 1;
        $searchNames['checkout.js'] = 1;
        $searchNames['script.js'] = 1;
        $searchNames['jquery-ui.js'] = 1;
        //$searchNames['dropin.min.js'] = 1;

        $navTimingsRes = $this->_getNavTimingsInRange($lastPageViewId + 1, $lastPageViewId + $this->scannedChunkSize);

        $decompressor = new Decompressor();

        foreach ($navTimingsRes as $nav) {
            $pageViewId = $nav['pageViewId'];



            /** @var ResourceTimings $resourceTimings */
            $resourceTimings = $this->registry
                ->getRepository(ResourceTimings::class)
                ->findBy(['pageViewId' => $pageViewId]);


            $resourceTimingsDecompressed = [];

            /** @var ResourceTimings $res */
            foreach ($resourceTimings as $res) {
                $resourceTimingsDecompressed = $decompressor->decompress($res->getResourceTimings());
            }

            $resourceTimingsData = [];

            $tmpName = '';
            $tmpEndTime = 0;
            $tmpUrlId   = 0;

            foreach ($resourceTimingsDecompressed as $res) {
                /** @var \App\Entity\ResourceTimingsAttributes $resourceTimingUrl */
                $resourceTimingUrl = $this->registry
                    ->getRepository(ResourceTimingsAttributes::class)
                    ->findOneBy(['id' => $res['url_id']]);

                $name = basename($resourceTimingUrl->getUrl());

                if (strpos($name, '.js') !== false || strpos($name, '.css') !== false) {

                    if (isset($searchNames[$name])) {
                        if (($res['start'] + $res['duration']) > $tmpEndTime) {
                            $tmpName = $name;
                            $tmpEndTime = $res['start'] + $res['duration'];
                            $tmpUrlId = $res['url_id'];
                        }
                    }
                }
            }

            if (!empty($tmpName)) {
                if ($nav['firstPaint'] > 0 && $tmpEndTime > $nav['firstPaint']) {
                    $resourceTimingsData[] = [
                        'page_view_id'          => $pageViewId,
                        'resource_url_id'       => $tmpUrlId,
                        'name'                  => $tmpName,
                        'responseEnd'           => $tmpEndTime,
                        'firstPaint'            => $nav['firstPaint']
                    ];

                    $badBeacons++;

                    print_r($resourceTimingsData);
                }


            }


        }

        var_dump($badBeacons);

        return count($navTimingsRes);
    }

    /**
     * @return bool|int
     */
    private function _getPreviousLastScannedPageViewId()
    {
        return 100;

        $repository = $this->registry
            ->getRepository(VisitsOverview::class);

        $pageViewId = (int) $repository->createQueryBuilder('vo')
            ->select('MAX(vo.lastPageViewId)')
            ->getQuery()
            ->getSingleScalarResult();

        return $pageViewId === 0 ? 0 : $pageViewId;
    }

    private function _saveVisits(array $visits)
    {
        $lastScannedDate = $this->_getPreviousLastScannedDate();

        $cnt = 1;

        foreach ($visits as $visit) {
            $cnt++;

            if (isset($visit['visitId'])) {
                $entity = $this->registry->getRepository(VisitsOverview::class)
                    ->find($visit['visitId']);
            } else {
                $entity = new VisitsOverview();
                $entity->setCompleted(false);
            }

            $entity->setGuid($visit['guid']);
            $entity->setpageViewsCount($visit['pageViewsCount']);
            $entity->setFirstPageViewId($visit['firstPageViewId']);
            $entity->setLastPageViewId($visit['lastPageViewId']);
            $entity->setFirstUrlId($visit['firstUrlId']);
            $entity->setLastUrlId($visit['lastUrlId']);
            $entity->setAfterLastVisitDuration($visit['afterLastVisitDuration']);

            //Check if we need to close the visit

//            var_dump($lastScannedDate);
            if (isset($visit['visitId']) && $lastScannedDate !== false) {
                $completed = $this->_isVisitCompleted($entity, $lastScannedDate);
                $entity->setCompleted($completed);

                if ($completed) {
                    $entity->setVisitDuration(
                        $this->_calculatePageViewsDurationDuration($visit['firstPageViewId'], $visit['lastPageViewId'])
                    );

                    $entity->setAfterLastVisitDuration(
                        $this->_calculateAfterLastVisitDuration(
                            $visit
                        )
                    );
                }
            }

            $this->registry->getManager()->persist($entity);

            if (($cnt % $this->batchSize) === 0) {
                $this->registry->getManager()->flush();
                $this->registry->getManager()->clear();
            }
        }

        $this->registry->getManager()->flush();
        $this->registry->getManager()->clear();
    }

    /**
     * @param int $startId
     * @param int $endId
     * @return mixed
     */
    private function _getNavTimingsInRange(int $startId, int $endId)
    {
        $repository = $this->registry
            ->getRepository(NavigationTimings::class);

        $query = $repository->createQueryBuilder('nt')
            ->where("nt.pageViewId >= '" . $startId . "' AND nt.pageViewId <= '" . $endId . "'")
            ->andWhere('nt.userAgentId NOT IN (:userAgentId)')
            ->setParameter('userAgentId', $this->_botUserAgentsIds())
            ->select(['nt.pageViewId', 'nt.firstPaint'])
            ->getQuery();

        return $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @return array
     */
    private function _botUserAgentsIds()
    {
        $userAgentRepo = $this->registry
            ->getRepository(NavigationTimingsUserAgents::class);

        $query = $userAgentRepo->createQueryBuilder('ua')
            ->where("ua.deviceType = 'bot'")
            ->select('ua.id')
            ->getQuery();

        $userAgents = $query->getResult();

        $userAgentsArr = [];

        foreach ($userAgents as $agent) {
            $userAgentsArr[] = $agent['id'];
        }

        return $userAgentsArr;
    }

}