<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Cache\Simple\FilesystemCache;

use App\Entity\NavigationTimings;
use App\Entity\NavigationTimingsUserAgents;

use DateTime;
use DatePeriod;
use DateInterval;

class BounceRateController extends AbstractController
{

    private function _getInMetricInPeriod($start, $end)
    {
        $botsUA = $this->_botUserAgents();

        $filteredUA = $this->_userAgents('mobile');

        $sessions = [];
        $bouncedSessions = [];

        $minId = $this->_getLowesIdInInterval($start, $end);
        $maxId = $this->_getHighestIdInInterval($start, $end);

        $repository = $this->getDoctrine()
            ->getRepository(NavigationTimings::class);

        $query = $repository->createQueryBuilder('nt')
            ->where("nt.pageViewId >= '" . $minId . "' AND nt.pageViewId <= '" . $maxId . "'")
            ->select(['nt.guid', 'nt.urlId', 'nt.processId', 'nt.firstPaint', 'nt.userAgentId', 'nt.createdAt'])
            ->orderBy('nt.guid, nt.createdAt', 'ASC')
            ->getQuery();

        $navigationTimings = $query->getResult();


//        echo '<pre>';
//        print_r($navigationTimings);

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

    private function _getBounces(array $guids, string $minId, string $maxId, array $filteredUA, array $botsUA)
    {
        $sessions        = [];
        $bouncedSessions = [];

        $repository = $this->getDoctrine()
            ->getRepository(NavigationTimings::class);

        $query = $repository->createQueryBuilder('nt')
            ->where("nt.guid IN (:guids) AND nt.pageViewId >= '" . $minId . "' AND nt.pageViewId <= '" . $maxId . "'")
            ->select(['nt.guid', 'nt.urlId', 'nt.processId', 'nt.firstPaint', 'nt.userAgentId', 'nt.createdAt'])
//            ->setParameter('guids', $guids)
            ->orderBy('nt.guid, nt.createdAt', 'ASC')
            ->getQuery();

        $navigationTimings = $query->getResult();


//        echo '<pre>';
//        print_r($navigationTimings);

        $scannedGuid = '';
        $viewsCount = 0;

        foreach ($navigationTimings as $nav) {
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
            'sessions'         => $sessions,
            'bounced_sessions' => $bouncedSessions
        ];
    }

    /**
     * @return array
     */
    private function _botUserAgents()
    {
        $userAgentRepo = $this->getDoctrine()
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
        $userAgentRepo = $this->getDoctrine()
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
     * @Route("/diagrams/bounce_rate/distribution", name="diagrams_bounce_rate_distribution")
     */
    public function firstPaintDistribution()
    {
        // Quick hack for out of memory problems
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $sessionsCount = 0;
        $bouncesCount = 0;
        $convertedSessions = 0;

        $dateConditionStart = '2019-01-28';
        $dateConditionEnd   = '2019-01-29';

        // Test periods
        $periodChunks = $this->_gerPeriodDays($dateConditionStart, $dateConditionEnd);

        $groupMultiplier = 200;
        $upperLimit = 7000;

        $firstPaintArr = [];
        $allFirstPaintArr = [];
        $bouncesGroup  = [];
        $bouncesPercents = [];

        // Init the groups/buckets
        for($i = $groupMultiplier; $i <= 7000; $i += $groupMultiplier) {
            $allFirstPaintArr[$i] = 0;
        }

        for($i = $groupMultiplier; $i <= $upperLimit; $i += $groupMultiplier) {
            $firstPaintArr[$i] = 0;
            $allFirstPaintArr[$i] = 0;
            if ($i >= 150 && $i <= $upperLimit) {
                $bouncesGroup[$i] = 0;
            }
        }

        foreach ($periodChunks as $day) {
            $cache = new FilesystemCache();

            $cacheKey = '133teadd2teru3e' . md5($day['start'] . $day['end']);

//            if ($cache->has($cacheKey)) {
//                $dayReport = $cache->get($cacheKey);
//            } else {
            $dayReport = $this->_getInMetricInPeriod($day['start'], $day['end']);
//                $cache->set($cacheKey, $dayReport);
//            }

            $convertedSessions += count($dayReport['converted_sessions']);

            foreach ($dayReport['all_sessions'] as $guid => $ttfp) {

                $paintGroup = $groupMultiplier * (int) ($ttfp / $groupMultiplier);

                if (7000 >= $paintGroup && $paintGroup > 0) {
                    $allFirstPaintArr[$paintGroup]++;
                }

                if ($upperLimit >= $paintGroup && $paintGroup > 0) {

                    if ($paintGroup >= 150 && $paintGroup  <= $upperLimit) {
                        $firstPaintArr[$paintGroup]++;
                        $sessionsCount++;

                        if (isset($dayReport['bounced_sessions'][$guid])) {
                            $bouncesCount++;

                            $bouncesGroup[$paintGroup]++;

                        }
                    }
                }
            }
        }

        $xAxisLabels = [];

        foreach($firstPaintArr as $paintGroup => $numberOfProbes) {
            $time = ($paintGroup / 1000);

            $xAxisLabels[] = $time;

            if ($numberOfProbes > 0) {
                if ($paintGroup >= 150 && $paintGroup <= $upperLimit) {
                    $bouncesPercents[$paintGroup] = (int) number_format(($bouncesGroup[$paintGroup] / $numberOfProbes) * 100);
                }
            }
        }

        return $this->render('diagrams/bounce_first_paint.html.twig',
            [
                'count'             => $sessionsCount,
                'bounceRate'        => (int) number_format(($bouncesCount / $sessionsCount) * 100),
                'conversionRate'    => (int) number_format(($convertedSessions / $sessionsCount) * 100),
                'x1Values'          => json_encode(array_keys($firstPaintArr)),
                'y1Values'          => json_encode(array_values($firstPaintArr)),
                'x2Values'          => json_encode(array_keys($bouncesPercents)),
                'y2Values'          => json_encode(array_values($bouncesPercents)),
                'annotations'       => json_encode($bouncesPercents),
                'x_axis_labels'     => json_encode(array_values($xAxisLabels)),
                'startDate'         => $dateConditionStart,
                'endDate'           => $dateConditionEnd
            ]
        );
    }

    private function _gerPeriodDays($startDate, $endDate)
    {
        $calendarDayFrom = $startDate;
        $calendarDayTo = $endDate;

        $period = new DatePeriod(
            new DateTime($calendarDayFrom),
            new DateInterval('P1D'),
            new DateTime($calendarDayTo)
        );

        $betweenArr = [];

        foreach ($period as $key => $value) {
            $calendarDay = $value->format('Y-m-d');

            $betweenArr[] = [
                'start' => $calendarDay . ' 00:00:01',
                'end'   => $calendarDay  . ' 23:59:59'
            ];
        }

        return $betweenArr;
    }

    private function _getHighestIdInInterval(string $start, string $end)
    {
        $repository = $this->getDoctrine()->getRepository(NavigationTimings::class);

        return $repository->createQueryBuilder('nt')
            ->select('MAX(nt.pageViewId)')
            ->where("nt.createdAt BETWEEN '" . $start . "' AND '" . $end . "'")
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function _getLowesIdInInterval(string $start, string $end)
    {
        $repository = $this->getDoctrine()->getRepository(NavigationTimings::class);

        return $repository->createQueryBuilder('nt')
            ->select('MIN(nt.pageViewId)')
            ->where("nt.createdAt BETWEEN '" . $start . "' AND '" . $end . "'")
            ->getQuery()
            ->getSingleScalarResult();
    }

}
