<?php

namespace App\Controller;

use App\BasicRum\Buckets;
use App\BasicRum\CollaboratorsAggregator;
use App\BasicRum\DiagramOrchestrator;
use App\Entity\NavigationTimings;
use App\Entity\NavigationTimingsUrls;
use App\Entity\VisitsOverview;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Routing\Annotation\Route;

class RevenueCalculatorController extends AbstractController
{
    /**
     * @Route("/diagrams/estimate/revenue_calculator", name="diagrams_estimate_revenue_calculator")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function revenueCalculator(DiagramOrchestrator $diagramOrchestrator)
    {
        // Quick hack for out of memory problems
        ini_set('memory_limit', -1);
        set_time_limit(0);

        $conversionIds = $this->getConversionUrlIds();

        $viewsCount = 0;
        $bouncesCount = 0;
        $convertedSessions = 0;

        $filterString = 'gclid=';

        $period = [
            [
                'from_date' => '01/01/2019',
                'to_date' => '03/01/2019',
            ],
        ];

        $requirements = [
            'periods' => $period,
            'filters' => [
                'query_param' => [
                    'search_value' => $filterString,
                    'condition' => 'contains',
                ],
                //                'device_type' => [
                //                    'search_value' => '3',
                //                    'condition'    => 'is'
                //                ]
            ],
            'business_metrics' => [
                'bounce_rate' => 1,
            ],
            'technical_metrics' => [
                'time_to_first_paint' => 1,
            ],
        ];

        $collaboratorsAggregator = new CollaboratorsAggregator();

        $collaboratorsAggregator->fillRequirements($requirements);

        $res = $diagramOrchestrator->load($collaboratorsAggregator->getCollaborators())->process();

        $bucketizer = new Buckets(200, 4600);
        $buckets = $bucketizer->bucketizePeriod($res[0], 'firstPaint');

        $bounces = [];

        foreach ($buckets as $bucketSize => $bucket) {
            $bounces[$bucketSize] = 0;
        }

        $bounceRatePercents = [];

        foreach ($buckets as $bucketSize => $bucket) {
            foreach ($bucket as $key => $sample) {
                ++$viewsCount;

                if (1 == $sample['pageViewsCount']) {
                    ++$bounces[$bucketSize];
                    ++$bouncesCount;
                    continue;
                }

                if ($this->hasConverted($sample, $conversionIds)) {
                    ++$convertedSessions;
                }
            }
        }

        foreach ($buckets as $bucketSize => $samples) {
            $firstPaintArr[$bucketSize] = \count($samples);
        }

        foreach ($buckets as $bucketSize => $bucket) {
            if (0 === \count($bucket)) {
                $bounceRatePercents[$bucketSize] = 0;
                continue;
            }

            $bounceRatePercents[$bucketSize] = (float) number_format(($bounces[$bucketSize] / \count($bucket)) * 100, 2);
        }

        $xAxisLabels = [0 => '0 sec', 1000 => '1 sec', 2000 => '2 sec', 3000 => '3 sec', 4000 => '4 sec'];

        $assumptions = [200, 400, 600, 800, 1000];

        $bounceRateAssumption = $this->_calculateEstimations($buckets, $bounceRatePercents, $assumptions);

        $startDate = new \DateTime($period[0]['from_date']);
        $endDate = new \DateTime($period[0]['to_date']);

        $formattedBounceRatePercents = [];

        foreach ($bounceRatePercents as $key => $val) {
            $formattedBounceRatePercents[$key] = (int) number_format($val, 0);
        }

        return $this->render('diagrams/diagram_first_paint.html.twig',
            [
                'count' => $viewsCount,
                'estimated_bounces' => $bounceRateAssumption,
                'bounceRate' => (int) number_format(($bouncesCount / $viewsCount) * 100),
                'conversionRate' => (int) number_format(($convertedSessions / $viewsCount) * 100),
                'x1Values' => json_encode(array_keys($firstPaintArr)),
                'y1Values' => json_encode(array_values($firstPaintArr)),
                'x2Values' => json_encode(array_keys($bounceRatePercents)),
                'y2Values' => json_encode(array_values($bounceRatePercents)),
                'annotations' => json_encode($formattedBounceRatePercents),
                'x_axis_values' => json_encode(array_keys($xAxisLabels)),
                'x_axis_labels' => json_encode(array_values($xAxisLabels)),
                'startDate' => $startDate->format('F jS, Y'),
                'endDate' => $endDate->format('F jS, Y'),
            ]
        );
    }

    private function hasConverted(array $sample, array $conversionIds): bool
    {
        $cache = new FilesystemAdapter('basicrum.revenue.estimator.cache');

        $guid = $sample['guid'];
        $firstPageViewId = $sample['firstPageViewId'];

        $dbUrlArr = explode('/', $_ENV['DATABASE_URL']);

        $cacheKey = end($dbUrlArr).$guid.$firstPageViewId;

        if ($cache->hasItem($cacheKey)) {
            $converted = $cache->getItem($cacheKey)->get();

            return 1 == $converted;
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

        return 1 == $converted;
    }

    /**
     * @return array
     */
    private function getConversionUrlIds()
    {
        $conversionUrl = 'checkout/onepage';

        $repository = $this->getDoctrine()
            ->getRepository(NavigationTimingsUrls::class);

        $res = $repository
            ->createQueryBuilder('ntu')
            ->where('ntu.url LIKE :url')
            ->setParameter('url', '%'.$conversionUrl.'%')
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

                $newFirsPaintArr[$newFirstPaint] += \count($probes);
            }

            foreach ($bouncesPercents as $paintGroup => $percent) {
                if (isset($newFirsPaintArr[$paintGroup])) {
                    $assumedSessions += $newFirsPaintArr[$paintGroup];
                    $assumedBounces += $newFirsPaintArr[$paintGroup] * $percent / 100;
                }
            }

            $bounces[$reduceAssumption] = (int) number_format(($assumedBounces / $assumedSessions) * 100);
        }

        return $bounces;
    }
}
