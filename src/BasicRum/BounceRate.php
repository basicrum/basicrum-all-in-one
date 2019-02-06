<?php

declare(strict_types=1);

namespace App\BasicRum;

use App\Entity\NavigationTimings;
use App\Entity\NavigationTimingsUserAgents;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class BounceRate
{

    /* @var \Doctrine\Bundle\DoctrineBundle\Registry $em */
    protected $em;

    /** @var \Symfony\Component\Cache\Adapter\FilesystemAdapter */
    protected $cache;

    /**
     * OneLevel Constructor
     *
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $em
     */
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $em)
    {
        $this->em = $em;
        $this->cache = new FilesystemAdapter('basicrum.report.cache');
    }

    /**
     * @param string $start
     * @param string $end
     * @param string $device
     * @return int|string
     */
    public function bounceRateInPeriod(string $start, string $end, string $device)
    {
        $data = $this->getInMetricInPeriod($start, $end, $device);

        $all = count($data['all_sessions']);
        $bounced =  count($data['bounced_sessions']);

        if ($all === 0) {
            return 0;
        }

        return number_format(($bounced / $all) * 100, 2);
    }

    public function getInMetricInPeriod($start, $end, $device = 'tablet')
    {
        $botsUA = $this->_botUserAgents();

        $filteredUA = $this->_userAgents($device);

        $sessions = [];
        $bouncedSessions = [];


        $cacheKey = 'bounce_day_report_' . md5($start . $end);

        if ($this->cache->hasItem($cacheKey)) {
            $navigationTimings = $this->cache->getItem($cacheKey)->get();
        } else {
            $minId = $this->_getLowesIdInInterval($start, $end);
            $maxId = $this->_getHighestIdInInterval($start, $end);

            $repository = $this->em
                ->getRepository(NavigationTimings::class);

            $query = $repository->createQueryBuilder('nt')
                ->where("nt.pageViewId >= '" . $minId . "' AND nt.pageViewId <= '" . $maxId . "'")
                ->select(['nt.guid', 'nt.urlId', 'nt.processId', 'nt.firstPaint', 'nt.userAgentId', 'nt.createdAt'])
                ->orderBy('nt.guid, nt.createdAt', 'ASC')
                ->getQuery();

            $navigationTimings = $query->getResult();

            $cacheItem = $this->cache->getItem($cacheKey);
            $cacheItem->set($navigationTimings);

            $this->cache->save($cacheItem);
        }

        $scannedGuid = 'test-guid';
        $viewsCount = 0;

        foreach ($navigationTimings as $nav) {
            if ( empty($nav['guid'] )) {
                continue;
            }

            if ( $scannedGuid !== $nav['guid']) {
                $viewsCount = 0;
            }

            if ($viewsCount >= 2) {
                continue;
            }

            $scannedGuid = $nav['guid'];
            $viewsCount++;

            if (!in_array((int) $nav['userAgentId'], $filteredUA)) {
                continue;
            }

            if (in_array((int) $nav['urlId'], [11773, 13])) {
                continue;
            }

            if (in_array((int) $nav['userAgentId'], $botsUA)) {
                continue;
            }

            $ttfp = $nav['firstPaint'];

            if ($ttfp == 0) {
                continue;
            }

            // Diff in minutes
//            if (count($navigationTimings) > 1) {
//                $startDiff  = strtotime($nav['createdAt']->format('Y-m-d H:i:s'));
//                $endDiff    = strtotime($nav['createdAt']->format('Y-m-d H:i:s'));
//
//                $minutes = round(abs($startDiff - $endDiff) / 60, 2);
//
//                if ($minutes > 30) {
//                    continue;
//                }
//            }

            if (!isset($sessions[$scannedGuid])) {
                $sessions[$scannedGuid] = $ttfp;
            }

            $bouncedSessions[$scannedGuid] = 1;

            if ($viewsCount > 1) {
                unset($bouncedSessions[$scannedGuid]);
            }
        }

        return [
            'all_sessions'          => $sessions,
            'bounced_sessions'      => $bouncedSessions,
            'converted_sessions'    => [],
            'visited_cart_sessions' => []
        ];
    }

    /**
     * @return array
     */
    private function _botUserAgents()
    {
        $userAgentRepo = $this->em
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

    /**
     * @param string $device
     * @param string $os
     * @return mixed
     */
    private function _userAgents(string $device, string $os = '')
    {
        $userAgentRepo = $this->em
            ->getRepository(NavigationTimingsUserAgents::class);

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $userAgentRepo->createQueryBuilder('ua');

        $qb->where("ua.deviceType = '{$device}'");

        if (!empty($os)) {
            $qb->andWhere("ua.osName = '{$os}'");
        }

        $query = $qb
            ->select('ua.id')
            ->getQuery();

        $userAgents = $query->getResult();

        $userAgentsMobileArr = [];

        foreach ($userAgents as $agent) {
            $userAgentsMobileArr[] = $agent['id'];
        }

        return $userAgentsMobileArr;
    }

    /**
     * @param string $start
     * @param string $end
     * @return mixed
     */
    private function _getHighestIdInInterval(string $start, string $end)
    {
        $repository = $this->em->getRepository(NavigationTimings::class);

        return $repository->createQueryBuilder('nt')
            ->select('MAX(nt.pageViewId)')
            ->where("nt.createdAt BETWEEN '" . $start . "' AND '" . $end . "'")
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param string $start
     * @param string $end
     * @return mixed
     */
    private function _getLowesIdInInterval(string $start, string $end)
    {
        $repository = $this->em->getRepository(NavigationTimings::class);

        return $repository->createQueryBuilder('nt')
            ->select('MIN(nt.pageViewId)')
            ->where("nt.createdAt BETWEEN '" . $start . "' AND '" . $end . "'")
            ->getQuery()
            ->getSingleScalarResult();
    }

}