<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\NavigationTimings;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Releases;
use App\Entity\VisitsOverview;
use App\Entity\NavigationTimingsUrls;

use App\BasicRum\CollaboratorsAggregator;
use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Buckets;
use App\BasicRum\Statistics\Median;

class LandingPagesPerformanceOverTimeController extends AbstractController
{

    /**
     * @Route("/landing_pages/overtimeMedian", name="landing_pages_overtimeMedian")
     */
    public function overtimeView()
    {
        // Quick hack for out of memory problems
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ini_set('display_errors', '1');

        $res = $this->_getPopularPages(20, 1, 8551494);

        $pages = [];

        foreach ($res as $urlId => $visits) {
            /** @var \App\Entity\NavigationTimingsUrls $navigationTimingUrl */
            $navigationTimingUrl = $this->getDoctrine()
                ->getRepository(NavigationTimingsUrls::class)
                ->findOneBy(['id' => $urlId]);

            $pages [$navigationTimingUrl->getUrl()] = $navigationTimingUrl->getUrl();
        }

        $pageDiagrams       = [];
        $bounceRateDiagrams = [];

        $metrics = [
            'time_to_first_byte',
            'time_to_first_paint'
        ];

        foreach ($pages as $pageName => $url) {

            foreach ($metrics as $metric) {
                $metricName = '';

                $pageTitle = '';

                if ($metric === 'time_to_first_byte') {
                    $pageTitle = $pageName;
                    $metricName = 'First Byte';
                }

                if ($metric === 'time_to_first_paint') {
                    $pageTitle = '';
                    $metricName = 'First Paint';
                }

                $res = $this->_pageOvertime($url, $metric);

                $maxYaxis = $res['ymax'] + 1000;
                $maxYaxis = 1000 * ((int) ($maxYaxis / 1000));

                $pageDiagrams[] = [
                    'section_title'       => $pageTitle,
                    'diagrams'            => json_encode($res['diagrams']),
                    'layout_extra_shapes' => json_encode($res['shapes']),
                    'title'               => $metricName  ." (median)",
                    'layout_overrides'    => json_encode(
                        [
                            'yaxis' => [
                                'range' => [0, $maxYaxis],
                                'tickvals' => [1000, 2000, 3000, 4000, 5000],
                                'ticktext' =>  ["1 sec", "2 sec", "3 sec", "4 sec", "5 sec"],
                                'fixedrange' => true
                            ]
                        ]
                    )
                ];

                $bounceRateDiagrams[] =json_encode($res['bounce_rate_diagrams']);
            }
        }

        return $this->render('diagrams/over_time.html.twig',
            [
                'diagrams'             => $pageDiagrams,
                'bounce_rate_diagrams' => $bounceRateDiagrams
            ]
        );
    }

    /**
     * @param string $url
     * @param string $metric
     *
     * @return array
     */
    private function _pageOvertime(string $url, string $metric)
    {
        $today = new \DateTime('04/28/2019');
        $past  = new \DateTime('04/07/2019');

        $bucketizer = new Buckets(1, 10000);
        $median = new Median();

        $deviceTypes = [
            2 => 'desktop',
            3 => 'tablet',
            1 => 'mobile'
        ];

        $diagramsByType = [];

        $bounceRateDiagrams = [];

        foreach ($deviceTypes as $deviceId => $device) {
            $requirementsArr = [
                'filters' => [
                    'device_type' => [
                        'condition'    => 'is',
                        'search_value' =>  (string) $deviceId
                    ],
                    'url' => [
                        'condition'    => 'is',
                        'search_value' =>  $url
                    ]
                ],
                'periods' => [
                    [
                        'from_date' => $past->format('Y-m-d'),
                        'to_date'   => $today->format('Y-m-d')
                    ]
                ],
                'technical_metrics' => [
                    'time_to_first_paint' => 1,
                    'time_to_first_byte'  => 1
                ],
//                'business_metrics'  => [
//                    'bounce_rate' => 1
//                ]
            ];


            $collaboratorsAggregator = new CollaboratorsAggregator();

            $collaboratorsAggregator->fillRequirements($requirementsArr);

            $diagramOrchestrator = new DiagramOrchestrator(
                $collaboratorsAggregator->getCollaborators(),
                $this->getDoctrine()
            );

            $res = $diagramOrchestrator->process();

            if ($metric === 'time_to_first_byte') {
                $searchKey = 'firstByte';
            }

            if ($metric === 'time_to_first_paint') {
                $searchKey = 'firstPaint';
            }

            foreach ($res as $daySamples) {
                foreach ($daySamples as $day => $samples) {
                    $buckets = $bucketizer->bucketize($samples, $searchKey);
                    $sampleDiagramValues = [];

                    foreach ($buckets as $bucketSize => $bucket) {
                        $sampleDiagramValues[$bucketSize] = count($bucket);
                    }

                    $diagramsByType[$device][$day] = $median->calculateMedian($sampleDiagramValues);
                }
            }
        }

        $maxYValues = [];

        foreach ($diagramsByType as $device => $data) {
            if ($metric === 'time_to_first_byte') {
                $extraname = 'First Byte';
            }

            if ($metric === 'time_to_first_paint') {
                $extraname = 'Start Render';
            }

            $name = ucfirst($device);

            $yValues = array_values($data);

            $maxYValues[] = max($yValues);

            $diagrams[] = [
                'name' => $name,
                'x'    => array_keys($data),
                'y'    => array_values($yValues)
            ];
        }

        $repository = $this->getDoctrine()
            ->getRepository(Releases::class);

        $start = $past;
        $end   = $today;

        $query = $repository->createQueryBuilder('r')
            ->where('r.date BETWEEN :start AND :end')
            ->setParameter('start', $start->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'))
            ->getQuery();

        $releases = $query->getResult();

        $shapes = [];

        // Defaults
        $releaseAnnotations = [
            'x'    => [],
            'y'    => [],
            'text' => [],
            'mode' => 'markers',
            'hoverinfo' => 'text',
            'showlegend' => false
        ];

        /** @var \App\Entity\Releases $release  */
        foreach ($releases as $release) {
            break;
            $releaseDates[] = $release->getDate();
            $shapes[] = [
                'type' => 'line',
                'x0'   => $release->getDate()->format('Y-m-d'),
                'y0'   => -0.5,
                'x1'   => $release->getDate()->format('Y-m-d'),
                'y1'   => 1400,
                'line' => [
                    'color' => '#ccc',
                    'width' =>  2,
                    'dash'  =>  'dot'
                ]
            ];

            $releaseAnnotations['x'][]    = $release->getDate()->format('Y-m-d');
            $releaseAnnotations['y'][]    = 1400;
            $releaseAnnotations['text'][] = $release->getDescription();
        }

//        $monthsBeginnings = [
//            new \DateTime('01/01/2019'),
//            new \DateTime('02/01/2019'),
//            new \DateTime('03/01/2019'),
//            new \DateTime('03/31/2019')
//        ];
//
//        /** @var \App\Entity\Releases $release  */
//        foreach ($monthsBeginnings as $beginning) {
//            $shapes[] = [
//                'type' => 'line',
//                'x0'   => $beginning->format('Y-m-d'),
//                'y0'   => -1.5,
//                'x1'   => $beginning->format('Y-m-d'),
//                'y1'   => 70,
//                'line' => [
//                    'color' => '#000',
//                    'width' =>  4,
//                ]
//            ];
//
////            $releaseAnnotations['x'][]    = $beginning->format('Y-m-d');
////            $releaseAnnotations['y'][]    = 1400;
////            $releaseAnnotations['text'][] = $release->getDescription();
//        }

        $diagrams[] = $releaseAnnotations;

        return [
            'diagrams' => $diagrams,
            'shapes'   => $shapes,
            'bounce_rate_diagrams' => $bounceRateDiagrams,
            'ymax' => max($maxYValues)
        ];
    }

    /**
     * @param int $count
     * @param int $minId
     * @param int $maxId
     * @return array
     */
    private function _getTopLandingPages(int $count, int $minId, int $maxId)
    {
        $repository = $this->getDoctrine()->getRepository(VisitsOverview::class);

        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        $queryBuilder = $repository->createQueryBuilder('vo');

        $queryBuilder
            ->select(['count(vo.firstUrlId) as visitsCount', 'vo.firstUrlId'])
            ->where("vo.firstPageViewId BETWEEN " . $minId . " AND " . $maxId)
            ->groupBy('vo.firstUrlId')
            ->orderBy('count(vo.firstUrlId)', 'DESC')
            ->setMaxResults($count)
            ->getQuery();

        $visits = $queryBuilder->getQuery()
            ->getResult();

        $popularLandingPages = [];

        foreach ($visits as $visit) {
            $popularLandingPages[$visit['firstUrlId']] = $visit['visitsCount'];
        }

        return $popularLandingPages;
    }

    private function _getPopularPages(int $count, int $minId, int $maxId)
    {
        $repository = $this->getDoctrine()->getRepository(NavigationTimings::class);

        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        $queryBuilder = $repository->createQueryBuilder('nt');

        $queryBuilder
            ->select(['count(nt.urlId) as visitsCount', 'nt.urlId'])
            ->where("nt.pageViewId BETWEEN " . $minId . " AND " . $maxId)
            ->groupBy('nt.urlId')
            ->orderBy('count(nt.urlId)', 'DESC')
            ->setMaxResults($count)
            ->getQuery();

        $visits = $queryBuilder->getQuery()
            ->getResult();

        $popularLandingPages = [];

        foreach ($visits as $visit) {
            $popularLandingPages[$visit['urlId']] = $visit['urlId'];
        }

        return $popularLandingPages;
    }

}
