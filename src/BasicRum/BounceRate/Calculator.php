<?php

declare(strict_types=1);

namespace App\BasicRum\BounceRate;

use App\Entity\NavigationTimings;
use App\Entity\VisitsOverview;
use App\Entity\NavigationTimingsUserAgents;


class Calculator
{

    /** @var int */
    private $scannedChunkSize = 1000;

    private $batchSize        = 500;

    /** @var int */
    private $sessionExpireMinutes = 30;

    /** @var  \Symfony\Bridge\Doctrine\RegistryInterface */
    private $registry;

    public function __construct(\Symfony\Bridge\Doctrine\RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function calculate()
    {
        /**
         * @todo: Validate first and last page view id time difference seems that there is a small glitch
         * When we scan big chunk if visits we may miss bounce sometimes because we my have expired sessions
         * in current scan
         */

        /**
         * We will use GUID for relation between `navigation_timings` and `visits_overview`
         *
         * We also will have 2 more fields in `visits_overview` that will point to `navigation_timings`.`page_view_id`
         *  - first_page_view_id
         *  - last_page_view_id
         *
         * 1. Select chunks from `navigation_timings`
         * 2. Select not completed records from `visits_overview`.
         *  - condition `visits_overview`.`completed` = 0
         * 3. Iterate `navigation_timings` chunks over NOT Completed  `visits_overview` entries
         *      - If `navigation_timings` does not exist in
         */

        $lastPageViewId = $this->_getPreviousLastScannedPageViewId();

        if ($lastPageViewId === false) {
            $lastPageViewId = 0;
        }

        $navTimingsRes = $this->_getNavTimingsInRange($lastPageViewId + 1, $lastPageViewId + $this->scannedChunkSize);

        $notCompletedVisits = $this->_getNotCompletedVisits();

        $currentGuid = 'test-guid';
        $viewsCount = 0;

        $visits = [];

        foreach ($navTimingsRes as $nav)
        {
            if (empty($nav['guid'])) {
                continue;
            }

            if ($nav['guid'] !== $currentGuid) {
                $currentGuid = $nav['guid'];
                $viewsCount = 0;
            }

            if ($viewsCount === 0) {
                if (!isset($notCompletedVisits[$currentGuid])) {
                    $visits[$currentGuid] = [
                        'guid'               => $currentGuid,
                        'pageViewsCount'     => 0,
                        'firstPageViewId'    => $nav['pageViewId']
                    ];
                } else {
                    $visits[$currentGuid] = [
                        'visitId'            => $notCompletedVisits[$currentGuid]['visitId'],
                        'guid'               => $currentGuid,
                        'pageViewsCount'     => $notCompletedVisits[$currentGuid]['pageViewsCount'],
                        'firstPageViewId'    => $notCompletedVisits[$currentGuid]['firstPageViewId']
                    ];
                }

                unset($notCompletedVisits[$currentGuid]);
            }

            $viewsCount++;

            $visits[$currentGuid]['pageViewsCount']++;
            $visits[$currentGuid]['lastPageViewId']         = $nav['pageViewId'];
            $visits[$currentGuid]['visitDuration']          = 0;
            $visits[$currentGuid]['afterLastVisitDuration'] = 0;
        }

        // Add old visits that we out of scanned range
        $visits = array_merge($visits, $notCompletedVisits);

        $this->_saveVisits($visits);

        return count($visits);
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
     * @param int $firstPageViewId
     * @param int $lastPageViewId
     * @return int
     */
    private function _calculatePageViewsDurationDuration($firstPageViewId, $lastPageViewId)
    {
        if ($firstPageViewId === $lastPageViewId) {
            return 0;
        }

        $repository = $this->registry
            ->getRepository(NavigationTimings::class);

        $query = $repository->createQueryBuilder('nt')
            ->where("nt.pageViewId IN (:pageViewIds)")
            ->setParameter('pageViewIds', [$firstPageViewId, $lastPageViewId])
            ->select(['nt.createdAt', 'nt.pageViewId'])
            ->getQuery();

        $res = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        /** @var \DateTime $firstPageView */
        $firstPageView = $res[0]['createdAt'];

        /** @var \DateTime $lastPageView */
        $lastPageView = $res[1]['createdAt'];

        return $lastPageView->getTimestamp() - $firstPageView->getTimestamp();
    }

    /**
     * @param array $visit
     * @return int
     */
    private function _calculateAfterLastVisitDuration(array $visit)
    {
        $repository = $this->registry
            ->getRepository(VisitsOverview::class);

        $query = $repository->createQueryBuilder('vo')
            ->where("vo.firstPageViewId < :firstPageViewId")
            ->andWhere(("vo.guid = (:guid)"))
            ->setParameter('firstPageViewId', $visit['firstPageViewId'])
            ->setParameter('guid', $visit['guid'])
            ->select(['vo.lastPageViewId'])
            ->orderBy('vo.lastPageViewId', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $res = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_SCALAR);


        if (empty($res)) {
            return 0;
        }

        return $this->_calculatePageViewsDurationDuration($res[0]['lastPageViewId'], $visit['firstPageViewId']);
    }

    /**
     * @param VisitsOverview $visit
     * @param \DateTime $lastScannedDate
     * @return bool
     */
    private function _isVisitCompleted(VisitsOverview $visit, \DateTime $lastScannedDate)
    {
        $lastVisitDate = $this->_getPageViewDate($visit->getLastPageViewId());

        // Check if there were no other visits in certain period of time
        $startDiff  = strtotime($lastScannedDate->format('Y-m-d H:i:s'));
        $endDiff    = strtotime($lastVisitDate->format('Y-m-d H:i:s'));

        $minutes = round(abs($startDiff - $endDiff) / 60, 2);

        return $this->sessionExpireMinutes < $minutes;
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
            ->select(['nt.guid', 'nt.createdAt', 'nt.pageViewId'])
            ->orderBy('nt.guid, nt.pageViewId', 'ASC')
            ->getQuery();

        return $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @return array
     */
    private function _getNotCompletedVisits()
    {
        $repository = $this->registry
            ->getRepository(VisitsOverview::class);

        $query = $repository->createQueryBuilder('vo')
            ->where("vo.completed = 0")
            ->select(['vo'])
            ->getQuery();

        $visits = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        $transformed = [];

        foreach ($visits as $visit) {
            $transformed[$visit['guid']] = $visit;
        }

        return $transformed;
    }

    /**
     * @param int $pageViewId
     * @return \DateTime
     */
    private function _getPageViewDate(int $pageViewId)
    {
        /** @var $entity $entity */
        $entity = $this->registry->getRepository(NavigationTimings::class)
            ->find($pageViewId);

        return empty($entity) ? false : $entity->getCreatedAt();
    }

    /**
     * @return \DateTime|false
     */
    private function _getPreviousLastScannedDate()
    {
        $pageViewId = $this->_getPreviousLastScannedPageViewId();

        return $pageViewId ? $this->_getPageViewDate($pageViewId) : false;
    }

    /**
     * @return bool|int
     */
    private function _getPreviousLastScannedPageViewId()
    {
        $repository = $this->registry
            ->getRepository(VisitsOverview::class);

        $pageViewId = (int) $repository->createQueryBuilder('vo')
            ->select('MAX(vo.lastPageViewId)')
            ->getQuery()
            ->getSingleScalarResult();

        return $pageViewId === 0 ? false : $pageViewId;
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