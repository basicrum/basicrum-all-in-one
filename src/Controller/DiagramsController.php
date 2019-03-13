<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\BasicRum\WaterfallSvgRenderer;

use App\BasicRum\ResourceTiming\Decompressor;
use App\BasicRum\ResourceSize;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

use App\Entity\NavigationTimings;
use App\Entity\ResourceTimings;
use App\Entity\ResourceTimingsUrls;
use App\Entity\NavigationTimingsUserAgents;

use App\BasicRum\CollaboratorsAggregator;
use App\BasicRum\DiagramOrchestrator;

use App\BasicRum\Buckets;

use App\Entity\NavigationTimingsQueryParams;
use App\Entity\NavigationTimingsUrls;
use App\Entity\VisitsOverview;

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
     * @Route("/diagrams/estimate/revenue_calculator", name="diagrams_estimate_revenue_calculator")
     */
    public function revenueCalculator()
    {
        // Quick hack for out of memory problems
        ini_set('memory_limit', -1);
        set_time_limit(0);

        $conversionIds = $this->getConversionUrlIds();

        $viewsCount     = 0;
        $bouncesCount      = 0;
        $convertedSessions = 0;

        $filterString = 'from=Classic';

        $period = [
            [
                'from_date'   => '01/24/2019',
                'to_date'     => '03/01/2019'
            ]
        ];

        $requirements = [
            'periods'      => $period,
//            'filters'     => [
//                'device_type' => [
//                    'search_value' => '2',
//                    'condition'    => 'isNot'
//                ]
//            ],
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

        $bucketizer = new Buckets(200, 4600);
        $buckets = $bucketizer->bucketizePeriod($res[0], 'firstPaint');

        $bounces  = [];

        foreach ($buckets as $bucketSize => $bucket) {
            $bounces[$bucketSize] = 0;
        }

        $bounceRatePercents = [];

        $cache = new FilesystemAdapter('basicrum.revenue.estimator.cache');

        $dbUrlArr = explode('/', getenv('DATABASE_URL'));

        $cachePrefix = end($dbUrlArr);

        // Filtering
        foreach ($buckets as $bucketSize => $bucket) {
//            break;
            foreach ($bucket as $key => $sample) {
                $pvid = $sample['pageViewId'];

                $cacheKey = $cachePrefix . 'query_param' . $pvid;
                $queryParams = '';

                if ($cache->hasItem($cacheKey)) {
                    $queryParams = $cache->getItem($cacheKey)->get();
                } else {
                    $pvidQuery = $this->getDoctrine()
                        ->getRepository(NavigationTimingsQueryParams::class)
                        ->find($pvid);


                    if (!empty($pvidQuery)) {
                        $queryParams = $pvidQuery->getQueryParams();
                    } else {
                        $queryParams = '';
                    }

                    $cacheItem = $cache->getItem($cacheKey);
                    $cacheItem->set($queryParams);

                    $cache->save($cacheItem);
                }



//                var_dump($queryParams);

                if (!empty($queryParams)) {
                    if (strpos($queryParams, 'gclid=') === false)
                    {
                        unset($buckets[$bucketSize][$key]);
                        continue;
                    }

//                    if (strpos($queryParams, $filterString) === false) {
//                        unset($buckets[$bucketSize][$key]);
//                        continue;
//                    }
                } else {
                    unset($buckets[$bucketSize][$key]);
                }
            }
        }

        foreach ($buckets as $bucketSize => $bucket) {
            foreach ($bucket as $key => $sample) {
                $viewsCount++;

                if ($sample['pageViewsCount'] == 1) {
                    $bounces[$bucketSize]++;
                    $bouncesCount++;
                    continue;
                }

                if ($this->hasConverted($sample, $conversionIds)) {
                    $convertedSessions++;
                }
            }
        }

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

        $xAxisLabels = [0 => '0 sec', 1000 => '1 sec', 2000 => '2 sec', 3000 => '3 sec', 4000 => '4 sec'];

        $assumptions = [200, 400, 600, 800, 1000];

        $bounceRateAssumption = $this->_calculateEstimations($buckets, $bounceRatePercents, $assumptions);

        $startDate = new \DateTime($period[0]['from_date']);
        $endDate   = new \DateTime($period[0]['to_date']);

        $formattedBounceRatePercents = [];

        foreach ($bounceRatePercents as $key => $val ) {
            $formattedBounceRatePercents[$key] = (int) number_format($val,0);
        }


        return $this->render('diagrams/diagram_first_paint.html.twig',
            [
                'count'             => $viewsCount,
                'estimated_bounces' => $bounceRateAssumption,
                'bounceRate'        => (int) number_format(($bouncesCount / $viewsCount) * 100),
                'conversionRate'    => (int) number_format(($convertedSessions / $viewsCount) * 100),
                'x1Values'          => json_encode(array_keys($firstPaintArr)),
                'y1Values'          => json_encode(array_values($firstPaintArr)),
                'x2Values'          => json_encode(array_keys($bounceRatePercents)),
                'y2Values'          => json_encode(array_values($bounceRatePercents)),
                'annotations'       => json_encode($formattedBounceRatePercents),
                'x_axis_values'     => json_encode(array_keys($xAxisLabels)),
                'x_axis_labels'     => json_encode(array_values($xAxisLabels)),
                'startDate'         => $startDate->format('F jS, Y'),
                'endDate'           => $endDate->format('F jS, Y')
            ]
        );
    }

    /**
     * @param array $sample
     * @return bool
     */
    private function hasConverted(array $sample, array $conversionIds) : bool
    {
        $cache = new FilesystemAdapter('basicrum.revenue.estimator.cache');

        $guid            = $sample['guid'];
        $firstPageViewId = $sample['firstPageViewId'];

        $dbUrlArr = explode('/', getenv('DATABASE_URL'));

        $cacheKey = end($dbUrlArr) . $guid . $firstPageViewId;

        if ($cache->hasItem($cacheKey)) {
            $converted = $cache->getItem($cacheKey)->get();
            return $converted == 1;
        }


        $repository = $this->getDoctrine()
            ->getRepository(VisitsOverview::class);

        $res = $repository
            ->createQueryBuilder('vo')
            ->where('vo.firstPageViewId = :firstPageViewId')
            ->andWhere('vo.guid = :guid')
            ->setParameter('firstPageViewId', $firstPageViewId)
            ->setParameter('guid', $guid)
            ->getQuery()
            ->getResult();

        $visit = $res[0];

        $lastPageViewId = $visit->getLastPageViewId();

        $repository = $this->getDoctrine()
            ->getRepository(NavigationTimings::class);

        $res = $repository
            ->createQueryBuilder('nt')
            ->where('nt.pageViewId >= :firstPageViewId')
            ->andWhere('nt.pageViewId <= :lastPageViewId')
            ->andWhere('nt.guid = :guid')
            ->andWhere('nt.urlId IN (:conversion_url_ids)')
            ->setParameter('firstPageViewId', $firstPageViewId)
            ->setParameter('lastPageViewId', $lastPageViewId)
            ->setParameter('guid', $guid)
            ->setParameter('conversion_url_ids', implode(',', $conversionIds))
            ->getQuery()
            ->getResult();

        $converted = !empty($res) ? 1 : 0;

        $cacheItem = $cache->getItem($cacheKey);
        $cacheItem->set($converted);

        $cache->save($cacheItem);

        return $converted == 1;
    }

    /**
     * @return array
     */
    private function getConversionUrlIds()
    {
        $conversionUrl = 'checkout/cart';


        $repository = $this->getDoctrine()
            ->getRepository(NavigationTimingsUrls::class);

        $res = $repository
            ->createQueryBuilder('ntu')
            ->where('ntu.url LIKE :url')
            ->setParameter('url', '%' . $conversionUrl . '%')
            ->getQuery()
            ->getResult();

        $ids = [];
        foreach ($res as $url) {
            $ids[] = $url->getId();
        }


        return $ids;
    }

    private function _calculateEstimations(array $firstPaintArr, array $bouncesPercents, array $assumptions)
    {

        $bounces = [];

        $minInterval = 400;

        foreach ($assumptions as $reduceAssumption) {
            // Calculate for reduce assumption
            $assumedBounces = 0;
            $assumedSessions = 0;
            $newFirsPaintArr = [];

            foreach ($firstPaintArr as $paintGroup => $probes) {
                $newFirstPaint = $paintGroup - $reduceAssumption;
                if ($minInterval >= $newFirstPaint) {
                    $newFirstPaint = $minInterval;
                }

                if (!isset($newFirsPaintArr[$newFirstPaint])) {
                    $newFirsPaintArr[$newFirstPaint] = 0;
                }

                $newFirsPaintArr[$newFirstPaint] += count($probes);
            }

            foreach ($bouncesPercents as $paintGroup => $percent) {

                if (isset($newFirsPaintArr[$paintGroup])) {
                    $assumedSessions += $newFirsPaintArr[$paintGroup];
                    $assumedBounces  += $newFirsPaintArr[$paintGroup] * $percent / 100;
                }
            }

            $bounces[$reduceAssumption] = (int) number_format(($assumedBounces / $assumedSessions) * 100);
        }

        return $bounces;
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
