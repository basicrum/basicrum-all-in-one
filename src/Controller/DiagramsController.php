<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\BasicRum\WaterfallSvgRenderer;
use App\BasicRum\ResourceTimingDecompressor_v_0_3_4;
use App\BasicRum\ResourceTiming\Decompressor;
use App\BasicRum\ResourceSize;
use Symfony\Component\Cache\Simple\FilesystemCache;

use App\Entity\PageTypeConfig;
use App\Entity\NavigationTimings;
use App\Entity\ResourceTimings;
use App\Entity\ResourceTimingsUrls;
use App\Entity\NavigationTimingsUserAgents;


use DateTime;
use DatePeriod;
use DateInterval;

class DiagramsController extends AbstractController
{

    /**
     * @Route("/diagrams/builder", name="diagrams_builder")
     */
    public function diagramsBuilder()
    {
        return $this->render('diagrams/diagram_builder.html.twig');
    }

    /**
     * @Route("/diagrams/release/compare", name="diagrams_release_compare")
     */
    public function releaseCompare()
    {
        $pageTypes = $this->getDoctrine()
            ->getRepository(PageTypeConfig::class)
            ->findAll();


        $prevPeriod = '{"0":"0.00","200":"0.33","400":"0.66","600":"1.16","800":"1.66","1000":"2.11","1200":"2.87","1400":"3.79","1600":"4.02","1800":"4.63","2000":"5.06","2200":"5.47","2400":"5.81","2600":"6.09","2800":"5.93","3000":"5.67","3200":"5.33","3400":"4.95","3600":"4.46","3800":"3.61","4000":"3.44","4200":"3.10","4400":"2.59","4600":"2.28","4800":"1.98","5000":"1.74","5200":"1.64","5400":"1.45","5600":"1.15","5800":"1.11","6000":"0.98","6200":"0.80","6400":"0.63","6600":"0.66","6800":"0.61","7000":"0.61","7200":"0.43","7400":"0.34","7600":"0.42","7800":"0.43","8000":"0.00"}';
        $nextPeriod = '{"0":"0.00","200":"0.37","400":"0.69","600":"1.40","800":"1.77","1000":"2.78","1200":"3.41","1400":"3.84","1600":"4.40","1800":"4.99","2000":"5.24","2200":"5.75","2400":"6.11","2600":"5.92","2800":"5.98","3000":"5.43","3200":"5.19","3400":"4.47","3600":"4.00","3800":"3.59","4000":"3.15","4200":"2.88","4400":"2.34","4600":"2.12","4800":"1.95","5000":"1.74","5200":"1.49","5400":"1.28","5600":"1.03","5800":"1.05","6000":"0.85","6200":"0.83","6400":"0.67","6600":"0.62","6800":"0.65","7000":"0.58","7200":"0.45","7400":"0.37","7600":"0.33","7800":"0.30","8000":"0.00"}';

        $prevPeriod = json_decode($prevPeriod, true);
        $nextPeriod = json_decode($nextPeriod, true);

        $prevPeriodKeys   = json_encode(array_keys($prevPeriod));
        $prevPeriodValues = json_encode(array_values($prevPeriod));

        $nextPeriodKeys   = json_encode(array_keys($nextPeriod));
        $nextPeriodValues = json_encode(array_values($nextPeriod));

        return $this->render('diagrams/release_compare.html.twig',
            [
                'prev_period_keys'   => $prevPeriodKeys,
                'prev_period_values' => $prevPeriodValues,
                'next_period_keys'   => $nextPeriodKeys,
                'next_period_values' => $nextPeriodValues,
                'page_types'         => $pageTypes
            ]
        );
    }

    private function _getInMetricInPeriod($start, $end, $conditionString)
    {
        $sessions = [];
        $bouncedSessions = [];
        $convertedSessions = [];
        $visitedCartSessions = [];

        $repository = $this->getDoctrine()
            ->getRepository(NavigationTimings::class);

        $query = $repository->createQueryBuilder('nt')
            ->select(['nt.guid', 'nt.ptFp', 'nt.speculativeFp'])
            ->where("nt.url LIKE '%" . $conditionString . "%' AND nt.userAgent NOT LIKE '%1Googlebot%' AND ((nt.ptFp > 0 AND nt.ptFp < 10000) OR (nt.speculativeFp > 0 AND nt.speculativeFp < 10000)) AND nt.createdAt BETWEEN '" . $start . "' AND '" . $end . "'")
            ->orderBy('nt.createdAt', 'DESC')
            ->groupBy('nt.guid')
            ->getQuery();

        $navigationTimings1 = $query->getResult();

        foreach ($navigationTimings1 as $nav) {

            $guid = $nav['guid'];

            $repository = $this->getDoctrine()
                ->getRepository(NavigationTimings::class);

            $query = $repository->createQueryBuilder('nt')
                ->where("nt.guid = :guid AND nt.createdAt BETWEEN '" . $start . "' AND '" . $end . "'")
                ->select(['nt.url', 'nt.pid'])
                ->setParameter('guid', $guid)
                ->orderBy('nt.createdAt', 'ASC')
                ->getQuery();

            $navigationTimings = $query->getResult();

            $ttfp = $nav['ptFp'];
            if ($ttfp <= 0 && $nav['speculativeFp'] > 0) {
                $ttfp = $nav['speculativeFp'] + 250;
            }

            $sessions[$guid] = $ttfp;

            // The session didn't start with Google shopping
            if (false && strpos($navigationTimings[0]['url'], $conditionString) === false) {
                //bounce logic / do not count
                $this->_printSession($navigationTimings, 'Didn\'t start with Google shopping');
                unset($sessions[$guid]);
                continue;
            }
            // End:
            // ==============================================================================


            // Start: Check if the person has first view but also came back later from google
            // ==============================================================================
            $shouldSkip = true;
            if (count($navigationTimings) >= 2 && ($navigationTimings[0]['url'] == $navigationTimings[1]['url'])) {
                foreach ($navigationTimings as $key => $timing) {
                    if ($key <= 2) {
                        continue;
                    }

                    if ((strpos($timing['url'], $conditionString) !== false)) {
                        $shouldSkip = false;
                        break;
                    }
                }
            }
            if ($shouldSkip === false) {
                unset($sessions[$guid]);
                continue;
            }

            if (count($navigationTimings) === 2) {
                if ($navigationTimings[0]['pid'] !== $navigationTimings[1]['pid']) {
                    continue;
                }
            }

            // End: Check if the person has first view but also came back later from google
            // ==============================================================================
            if (count($navigationTimings) <= 2) {
                // @todo: Check also process. Sometimes the browser does not send beacon when user leaves the page
//

                $bouncedSessions[$guid] = 1;
            }

            foreach ($navigationTimings as $key => $timing) {
                if (strpos($timing['url'], 'checkout/cart') !== false) {
                    $visitedCartSessions[$guid] = 1;
                }
            }

            // Calculate conversion
            foreach ($navigationTimings as $key => $timing) {
                if (strpos($timing['url'], '/success') !== false) {
                    $convertedSessions[$guid] = 1;
                }
            }
        }

        return [
            'all_sessions'          => $sessions,
            'bounced_sessions'      => $bouncedSessions,
            'converted_sessions'    => $convertedSessions,
            'visited_cart_sessions' => $visitedCartSessions
        ];
    }


    /**
     * @Route("/diagrams/first_paint/distribution", name="diagrams_first_paint_distribution")
     */
    public function firstPaintDistribution()
    {
        // Quick hack for out of memory problems
        ini_set('memory_limit', -1);
        set_time_limit(0);

        $reduceAssumption = 600;

        $sessionsCount = 0;
        $bouncesCount = 0;
        $convertedSessions = 0;

        $conditionString = 'psm=GOO-0816-09';
        $conditionString = 'psm=';

        $dateConditionStart = '2018-08-01';
        $dateConditionEnd   = '2018-08-31';

        // Test periods
        $periodChunks = $this->_gerPeriodDays($dateConditionStart, $dateConditionEnd);

        $groupMultiplier = 200;
        $upperLimit = 3600;

        $firstPaintArr = [];
        $allFirstPaintArr = [];
        $bouncesGroup  = [];
        $bouncesPercents = [];

        // Init the groups/buckets
        for($i = $groupMultiplier; $i <= 10000; $i += $groupMultiplier) {
            $allFirstPaintArr[$i] = 0;
        }

        for($i = $groupMultiplier; $i <= $upperLimit; $i += $groupMultiplier) {
            $firstPaintArr[$i] = 0;
            $allFirstPaintArr[$i] = 0;
            if ($i >= 800 && $i <= 3600) {
                $bouncesGroup[$i] = 0;
            }
        }

        foreach ($periodChunks as $day) {
            $cache = new FilesystemCache();

            $cacheKey = 'teadd2teru3e' . md5($day['start'] . $day['end']);

            if (true && $cache->has($cacheKey)) {
                $dayReport = $cache->get($cacheKey);
            } else {
                $dayReport = $this->_getInMetricInPeriod($day['start'], $day['end'], $conditionString);
                $cache->set($cacheKey, $dayReport);
            }


            $convertedSessions += count($dayReport['converted_sessions']);

            foreach ($dayReport['all_sessions'] as $guid => $ttfp) {


                $paintGroup = $groupMultiplier * (int) ($ttfp / $groupMultiplier);

                if (10000 >= $paintGroup && $paintGroup > 0) {
                    $allFirstPaintArr[$paintGroup]++;
                }

                if ($upperLimit >= $paintGroup && $paintGroup > 0) {

                    if ($paintGroup >= 800 && $paintGroup  <= 3600) {
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
                if ($paintGroup >= 800 && $paintGroup <= 3600) {
                    $bouncesPercents[$paintGroup] = (int) number_format(($bouncesGroup[$paintGroup] / $numberOfProbes) * 100);
                }
            }
        }

        $assumptions = [200, 400, 600, 800, 1000];

        $bounceRateAssumption = $this->_calculateEstimations($firstPaintArr, $bouncesPercents, $assumptions, $allFirstPaintArr);

        return $this->render('diagrams/diagram_first_paint.html.twig',
            [
                'count'             => $sessionsCount,
                'estimated_bounces' => $bounceRateAssumption,
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

    private function _printSession($pageViews, $label)
    {
        return;
        echo '<pre>';
        echo $label . ":" . "\n";
        echo 'Guid: ' . $pageViews[0]->getGuid() . "\n";

        foreach ($pageViews as $page) {
            echo $page->getCreatedAt()->format('Y-m-d H:i:s') . " : " . $page->getUrl() . "\n";
        }
        echo '=================' . "\n";
        echo ' ' . "\n";
        echo '</pre>';
    }


    private function _calculateEstimations(array $firstPaintArr, array $bouncesPercents, array $assumptions, $allFirstPaintArr)
    {
        //print_r($firstPaintArr);

        $bounces = [];

        $minInterval = 800;
        $maxInterval = 3600;

        foreach ($assumptions as $reduceAssumption) {
            // Calculate for reduce assumption
            $assumedBounces = 0;
            $assumedSessions = 0;
            $newFirsPaintArr = [];

            foreach ($firstPaintArr as $paintGroup => $numberOfProbes) {
                $newFirstPaint = $paintGroup - $reduceAssumption;
                if ($minInterval > $newFirstPaint || $maxInterval < $newFirstPaint) {
                    $newFirstPaint = $paintGroup;
                }

                if (!isset($newFirsPaintArr[$newFirstPaint])) {
                    $newFirsPaintArr[$newFirstPaint] = 0;
                }

                $newFirsPaintArr[$newFirstPaint] += $numberOfProbes;
            }


            //add here the missing right side probes
//            $keys = array_keys($newFirsPaintArr);
//            $lastPaintGroup = $keys[count($keys) - 1];
//
//            foreach ($allFirstPaintArr as $paintGroup => $numberOfProbes) {
//                if ($lastPaintGroup < $paintGroup && $paintGroup <= 3600) {
//                    $newFirsPaintArr[$paintGroup] = $numberOfProbes;
//                }
//            }


            foreach ($bouncesPercents as $paintGroup => $percent) {
                if (isset($newFirsPaintArr[$paintGroup])) {
//                    var_dump($paintGroup);
                    $assumedSessions += $newFirsPaintArr[$paintGroup];
                    $assumedBounces += $newFirsPaintArr[$paintGroup] * $percent / 100;
                }
            }

//            echo '<pre>';
//            print_r($newFirsPaintArr);
//            echo '</pre>';

            $bounces[$reduceAssumption] = (int) number_format(($assumedBounces / $assumedSessions) * 100);
        }

        return $bounces;
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

    /**
     * @Route("/diagrams/beacon/draw", name="diagrams_beacon_draw")
     */
    public function beaconDraw()
    {
        $pageViewId = $_POST['page_view_id'];

        /** @var NavigationTimings $navigationTiming */
        $navigationTiming = $this->getDoctrine()
            ->getRepository(NavigationTimings::class)
            ->findBy(['pageViewId' => $pageViewId]);


        /** @var ResourceTimings $resourceTimings */
        $resourceTimings = $this->getDoctrine()
            ->getRepository(ResourceTimings::class)
            ->findBy(['pageViewId' => $pageViewId]);

        /** @var NavigationTimingsUserAgents $userAgent */
        $userAgent = $this->getDoctrine()
            ->getRepository(NavigationTimingsUserAgents::class)
            ->findBy(['id' => $navigationTiming[0]->getUserAgentId()]);

        $decompressor = new Decompressor();

        $resourceTimingsDecompressed = [];

        /** @var ResourceTimings $res */
        foreach ($resourceTimings as $res) {
            $resourceTimingsDecompressed = $decompressor->decompress($res->getResourceTimings());
        }

        $resourceTimingsData = [];

        foreach ($resourceTimingsDecompressed as $res) {
            // We do this in guly way but so far nice looking code is not a priority
            /** @var \App\Entity\ResourceTimingsUrls $resourceTimingUrl */
            $resourceTimingUrl = $this->getDoctrine()
                ->getRepository(ResourceTimingsUrls::class)
                ->findOneBy(['id' => $res['url_id']]);

            $resourceTimingsData[] = [
                'name'                  => $resourceTimingUrl->getUrl(),
                'initiatorType'         => 1,
                'startTime'             => $res['start'],
                'redirectStart'         => 0,
                'redirectEnd'           => 0,
                'fetchStart'            => 0,
                'domainLookupStart'     => 0,
                'domainLookupEnd'       => 0,
                'connectStart'          => 0,
                'secureConnectionStart' => 0,
                'connectEnd'            => 0,
                'requestStart'          => 0,
                'responseStart'         => 0,
                'responseEnd'           => $res['start'] + $res['duration'],
                'duration'              => $res['duration'],
                'encodedBodySize'       => 5,
                'transferSize'          => 11,
                'decodedBodySize'       => 52
            ];
        }


        $sizeDistribution = [];

        if (!empty($resourceTimingsData)) {
            $resourceSizesCalculator = new ResourceSize();
            $sizeDistribution = $resourceSizesCalculator->calculateSizes($resourceTimingsData);
        }

        $timings = [
            'nt_nav_st'      => 0,
            'nt_first_paint' => $navigationTiming[0]->getFirstContentfulPaint(),
            'nt_res_st'      => $navigationTiming[0]->getFirstByte(),
            'restiming'      => $resourceTimingsData,
            'url'            => 'https://www.darvart.de/'
        ];

        $renderer = new WaterfallSvgRenderer();

        $response = new Response(
            json_encode(
                [
                    'waterfall'             => $renderer->render($timings),
                    'resource_distribution' =>
                        [
                            'labels' => array_keys($sizeDistribution),
                            'values' => array_values($sizeDistribution)
                        ],
                    'user_agent'            => $userAgent[0]->getUserAgent(),
                    'browser_name'          => $userAgent[0]->getBrowserName()
                ]
            )
        );

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}
