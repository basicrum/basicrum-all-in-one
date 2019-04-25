<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\NavigationTimings;

use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\CollaboratorsAggregator;

use App\BasicRum\Buckets;
use App\BasicRum\Statistics\Median;

class DashboardController extends AbstractController
{

    private $pastDate   = '04/07/2019';
    private $todayDate  = '04/25/2019';

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index()
    {
        return $this->render('waterfall.html.twig',
            [
//                'popular_pages_performance' => $this->getPagesPerformanceData($this->tenMostPopularVisitedPages()),
//                'device_samples'            => json_encode($this->deviceSamples()),
                'last_page_views'           => $this->lastPageViewsListHTML()
            ]
        );
    }

    private $_deviceMapping = [
        '2' => 'Desktop',
        '3' => 'Tablet',
        '1' => 'Mobile',
        '4' => 'Bot'
    ];

    /**
     * @Route("/dashboard/device/distribution", name="dashboard_device_distribution")
     */
    public function deviceDistribution()
    {
        // Quick hack for out of memory problems
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $colors = [
            'Desktop' => 'rgb(31, 119, 180)',
            'Tablet'  => 'rgb(255, 127, 14)',
            'Mobile'  => 'rgb(44, 160, 44)',
            'Bot'     => 'rgb(0, 0, 0)'
        ];

        $past   = $this->pastDate;
        $today  = $this->todayDate;

        $period = [
            [
                'from_date' => $past,
                'to_date'   => $today
            ]
        ];

        $deviceSamples = [];
        $daysCount     = [];

        foreach ($this->_deviceMapping as $key => $device) {
            //Domain logic

            $requirements = [
                'periods'     => $period,
                'filters'     => [
                    'device_type' => [
                        'search_value' => (string) $key,
                        'condition'    => 'is'
                    ]
                ],
                'business_metrics' => [
                    'page_views_count' => 1
                ]
            ];

            $collaboratorsAggregator = new CollaboratorsAggregator();
            $collaboratorsAggregator->fillRequirements($requirements);


            $diagramOrchestrator = new DiagramOrchestrator(
                $collaboratorsAggregator->getCollaborators(),
                $this->getDoctrine()
            );

            $res = $diagramOrchestrator->process();

            $data = [];

            foreach ($res[0]  as $day => $samplesCount) {
                $data[$day] = $samplesCount[0]['count'];

                // Summing total visits per day. Used later for calculating percentage
                $daysCount[$day] = isset($daysCount[$day]) ? $daysCount[$day] + $data[$day] : $data[$day];
            }

            $deviceSamples[$device] = $data;
        }

        foreach ($deviceSamples as $device => $data) {
            foreach ($data as $day => $c) {
                if ($daysCount[$day] == 0) {
                    $deviceSamples[$device][$day] = '0.00';
                    continue;
                }

                $deviceSamples[$device][$day] = number_format(($c / $daysCount[$day]) * 100, 2);
            }
        }

        // Presentation logic
        $deviceDiagrams = [];

        foreach ($deviceSamples as $device => $data) {
            $deviceDiagrams[] = [
                'x'          => array_keys($data),
                'y'          => array_values($data),
                'name'       => $device,
                'stackgroup' => 'device',
                'line'       => [
                    'color'  => $colors[$device]
                ]
            ];
        }

        $response = new Response(json_encode($deviceDiagrams));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    private function lastPageViewsListHTML()
    {
        $collaboratorsAggregator = new CollaboratorsAggregator();

        $requirementsArr = [
            'filters' => [
                'device_type' => [
                    'condition'    => 'is',
                    'search_value' => '1'
                ],
//                'os_name' => [
//                    'condition'    => 'is',
//                    'search_value' => 'iOS'
//                ],
//                'device_manufacturer' => [
//                    'condition'    => 'is',
//                    'search_value' => 'Huawei'
//                ],
//                'browser_name' => [
//                    'condition'    => 'is',
//                    'search_value' => 'Chrome Dev'
//                ]
            ],
            'periods' => [
                [
                    'from_date'   => $this->pastDate,
                    'to_date'     => $this->todayDate
                ]
            ],
            'technical_metrics' => [
                'time_to_first_paint' => 1
            ],
            'business_metrics'  => [
                'bounce_rate'       => 1,
                'stay_on_page_time' => 1
            ]
        ];

        $collaboratorsAggregator->fillRequirements($requirementsArr);

        $diagramOrchestrator = new DiagramOrchestrator($collaboratorsAggregator->getCollaborators(), $this->getDoctrine());

        $res = $diagramOrchestrator->process();

        $sessionsCount = 0;
        $bouncesCount = 0;

        $groupMultiplier = 100;
        $upperLimit = 5000;

        $firstPaintArr = [];
        $allFirstPaintArr = [];
        $bouncesGroup  = [];
        $bouncedPageViews = [];

        // Init the groups/buckets
        for($i = $groupMultiplier; $i <= $upperLimit; $i += $groupMultiplier) {
            $allFirstPaintArr[$i] = 0;
        }

        for($i = $groupMultiplier; $i <= $upperLimit; $i += $groupMultiplier) {
            $firstPaintArr[$i] = 0;
            $allFirstPaintArr[$i] = 0;
            if ($i >= 250 && $i <= $upperLimit) {
                $bouncesGroup[$i] = 0;
            }
        }

        foreach ($res[0] as $day) {
            foreach ($day as $row) {
                $ttfp  = $row['firstPaint'];

                $paintGroup = $groupMultiplier * (int) ($ttfp / $groupMultiplier);

                if ($upperLimit >= $paintGroup && $paintGroup > 0) {
                    $allFirstPaintArr[$paintGroup]++;
                }

                if ($upperLimit >= $paintGroup && $paintGroup > 0) {

                    if ($paintGroup >= 250 && $paintGroup  <= $upperLimit) {
                        $firstPaintArr[$paintGroup]++;
                        $sessionsCount++;

                        if ($row['pageViewsCount'] == 1) {
//                            if ($paintGroup >= 1200 && $paintGroup <= 2200) {
                                $bouncedPageViews[] = $row['pageViewId'];
//                            }

                            $bouncesCount++;
                            $bouncesGroup[$paintGroup]++;
                        }
                    }
                }
            }
        }

        $repository = $this->getDoctrine()
            ->getRepository(NavigationTimings::class);

        $query = $repository->createQueryBuilder('nt')
            ->orderBy('nt.pageViewId', 'DESC')
            ->where('nt.pageViewId IN (' . implode(',', $bouncedPageViews) . ')')
            ->setMaxResults(200)
            ->getQuery();

        $navigationTimings = $query->getResult();

        $navTimingsFiltered = [];

        foreach ($navigationTimings as $navTiming) {
            if ($navTiming->getFirstContentfulPaint() > 0) {
                $navTimingsFiltered[] = $navTiming;
            }
        }

        return $this->get('twig')->render(
            'diagrams/waterfalls_list.html.twig',
            [
                'page_views'     => $navTimingsFiltered,
                'device_mapping' => $this->_deviceMapping
            ]
        );
    }

    /**
     * @Route("/dashboard/device/performance", name="dashboard_device_performance")
     */
    public function devicePerformance()
    {
        $bucketizer = new Buckets(1, 10000);
        $median = new Median();

        // Quick hack for out of memory problems
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $past   = $this->pastDate;
        $today  = $this->todayDate;

        $period = [
            [
                'from_date' => $past,
                'to_date'   => $today
            ]
        ];

        $deviceTypeId = $_POST['device_type'];

        //Domain logic
        $requirements = [
            'periods'     => $period,
            'filters'     => [
                'device_type' => [
                    'search_value' => (string) $deviceTypeId,
                    'condition'    => 'is'
                ]
            ],
            'technical_metrics' => [
                'time_to_first_paint' => 1,
                'time_to_first_byte'  => 1
            ]
        ];

        $collaboratorsAggregator = new CollaboratorsAggregator();
        $collaboratorsAggregator->fillRequirements($requirements);

        $diagramOrchestrator = new DiagramOrchestrator(
            $collaboratorsAggregator->getCollaborators(),
            $this->getDoctrine()
        );

        $res = $diagramOrchestrator->process();

        $metrics = [
            'firstByte',
            'firstPaint'
        ];

        $data = [];

        foreach ($metrics as $searchKey) {
            $data[$searchKey] = [];

            foreach ($res as $daySamples) {
                foreach ($daySamples as $day => $samples) {
                    $buckets = $bucketizer->bucketize($samples, $searchKey);
                    $sampleDiagramValues = [];

                    foreach ($buckets as $bucketSize => $bucket) {
                        $sampleDiagramValues[$bucketSize] = count($bucket);
                    }

                    $data[$searchKey][$day] = $median->calculateMedian($sampleDiagramValues);
                }
            }
        }

        // Presentation logic
        $deviceDiagrams = [];

        $deviceDiagrams[] = [
            'name'       => 'First Paint',
            'x'          => array_keys($data['firstPaint']),
            'y'          => array_values($data['firstPaint']),
            'type'       => 'bar',
            'marker'       => [
                'color'      => '#c141cd'
            ]
        ];

        $deviceDiagrams[] = [
            'name'       => 'First Byte',
            'x'          => array_keys($data['firstByte']),
            'y'          => array_values($data['firstByte']),
            'type'       => 'bar',
            'marker'       => [
                'color'      => '#1ec659'
            ]
        ];

        $response = new Response(json_encode($deviceDiagrams));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/dashboard/kpi_bounce_rate", name="dashboard_kpi_bounce_rate")
     */
    public function kpiBounceRate()
    {
        // Quick hack for out of memory problems
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $viewsCount     = 0;
        $bouncesCount   = 0;

        $requirements = [
            'periods' => [
                [
                    'from_date'   => $this->pastDate,
                    'to_date'     => $this->todayDate
                ]
            ],
            'business_metrics' => [
                'bounce_rate' => 1
            ],
            'technical_metrics' => [
                'time_to_first_paint' => 1
            ]
        ];

        $collaboratorsAggregator = new CollaboratorsAggregator();

        $collaboratorsAggregator->fillRequirements($requirements);

        $diagramOrchestrator = new DiagramOrchestrator(
            $collaboratorsAggregator->getCollaborators(),
            $this->getDoctrine()
        );

        $res = $diagramOrchestrator->process();

        $bucketizer = new Buckets(200, 5000);
        $buckets = $bucketizer->bucketizePeriod($res[0], 'firstPaint');

        $bounces  = [];

        foreach ($buckets as $bucketSize => $bucket) {
            $bounces[$bucketSize] = 0;
        }

        $bounceRatePercents = [];

        foreach ($buckets as $bucketSize => $bucket) {
            foreach ($bucket as $key => $sample) {
                $viewsCount++;

                if ($sample['pageViewsCount'] == 1) {
                    $bounces[$bucketSize]++;
                    $bouncesCount++;
                    continue;
                }
            }
        }

        $firstPaintArr = [];

        foreach ($buckets as $bucketSize => $samples) {
            $firstPaintArr[$bucketSize] = count($samples);
        }

        foreach ($buckets as $bucketSize => $bucket) {
            if (count($bucket) === 0) {
                $bounceRatePercents[$bucketSize] = 0;
                continue;
            }

            $bounceRatePercents[$bucketSize] = (float) number_format(($bounces[$bucketSize] / count($bucket)) * 100, 2);
        }

        $xAxisLabels = [
            0 => '0 sec',
            1000 => '1 sec',
            2000 => '2 sec',
            3000 => '3 sec',
            4000 => '4 sec',
            5000 => '5 sec',
            6000 => '6 sec',
            7000 => '7 sec',
            8000 => '8 sec',
        ];

        $xAxis = [
            'tickvals' => array_keys($xAxisLabels),
            'ticktext' => array_values($xAxisLabels)
        ];

        $formattedBounceRatePercents = [];

        foreach ($bounceRatePercents as $key => $val ) {
            $formattedBounceRatePercents[$key] = (int) number_format($val,0);
        }

        $diagrams = [
            [
                'name'       => 'First Paint',
                'x'          => array_keys($firstPaintArr),
                'y'          => array_values($firstPaintArr),
                'type'       => 'line',
                'marker'       => [
                    'color'      => '#c141cd'
                ]
            ],
            [
                'name'       => 'Bounce Rate',
                'x'          => array_keys($formattedBounceRatePercents),
                'y'          => array_values($formattedBounceRatePercents),
                'type'       => 'line',
                'yaxis'      => 'y2'
            ]
        ];

        $response = new Response(json_encode([
                    'diagrams' => $diagrams,
                    'xaxis'    => $xAxis
                ]
            )
        );

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}
