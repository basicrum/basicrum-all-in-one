<?php

declare(strict_types=1);

namespace App\Controller;

use App\BasicRum\Buckets;
use App\BasicRum\CollaboratorsAggregator;
use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Statistics\Median;
use App\Entity\NavigationTimings;
use App\Entity\NavigationTimingsUrls;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TopPagesPerformanceController extends AbstractController
{
    /**
     * @Route("/top_page/performance", name="top_page_performance")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(DiagramOrchestrator $diagramOrchestrator)
    {
        $date = '01/24/2019';
        $offsetDys = 24;

        return $this->render('top_pages/performance_table.html.twig',
            [
                'popular_pages_performance' => $this->getPagesPerformanceData(
                    $this->tenMostPopularVisitedPages($date, $offsetDys),
                    $date,
                    $offsetDys,
                    $diagramOrchestrator
                ),
            ]
        );
    }

    /**
     * @return array
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getPagesPerformanceData(array $data, string $date, int $offsetDays, DiagramOrchestrator $diagramOrchestrator)
    {
        $pageViewsPerformance = [];

        $pageNumber = 1;

        $markerDate = new \DateTime($date);
        $futureDate = $markerDate->modify("+{$offsetDays} days");

        $markerDate = new \DateTime($date);
        $pastDate = $markerDate->modify("-{$offsetDays} days");

        $markerDate = new \DateTime($date);

        $allVisitsCount = $this->countViewsInPeriod($markerDate, $futureDate)['visitsCount'];

        foreach ($data as $urlId => $count) {
            /** @var \App\Entity\NavigationTimingsUrls $navigationTimingUrl */
            $navigationTimingUrl = $this->getDoctrine()
                ->getRepository(NavigationTimingsUrls::class)
                ->findOneBy(['id' => $urlId]);

            $metrics = [
                'time_to_first_byte',
                'time_to_first_paint',
            ];

            $recentSamples = $this->periodForUrl($navigationTimingUrl->getUrl(), $markerDate, $futureDate, $metrics, $diagramOrchestrator);
            $oldSamples = $this->periodForUrl($navigationTimingUrl->getUrl(), $pastDate, $markerDate, $metrics, $diagramOrchestrator);

            //@todo: Move this to some decorator logic or use TWIG if possible
            $firstByteDiff = $oldSamples['time_to_first_byte'] - $recentSamples['time_to_first_byte'];
            $firstPaintDiff = $oldSamples['time_to_first_paint'] - $recentSamples['time_to_first_paint'];

            $firstByteDiff = number_format($firstByteDiff / 1000, 2);
            $firstPaintDiff = number_format($firstPaintDiff / 1000, 2);

            $firstByteDiffStyle = ('-0.00' === $firstByteDiff || '0.00' === $firstByteDiff) ? '' : ($firstByteDiff > 0 ? 'color: red;' : 'color: green;');
            $firstPaintDiffStyle = ('-0.00' === $firstPaintDiff || '0.00' === $firstPaintDiff) ? '' : ($firstPaintDiff > 0 ? 'color: red;' : 'color: green;');

            $firstByteDiff = ('-0.00' == $firstByteDiff) ? '0.00' : ($firstByteDiff > 0 ? '+ '.$firstByteDiff : $firstByteDiff);
            $firstPaintDiff = ('-0.00' == $firstPaintDiff) ? '0.00' : ($firstPaintDiff > 0 ? '+ '.$firstPaintDiff : $firstPaintDiff);

            $urlParsed = parse_url($navigationTimingUrl->getUrl());

            $pageViewsPerformance[] = [
                'number' => $pageNumber++,
                'page_views' => number_format(($count / $allVisitsCount) * 100, 2).' %',
                'first_byte_median' => number_format($recentSamples['time_to_first_byte'] / 1000, 2).' s',
                'first_byte_diff' => $firstByteDiff.' s',
                'first_byte_diff_style' => $firstByteDiffStyle,
                'first_paint_median' => number_format($recentSamples['time_to_first_paint'] / 1000, 2).' s',
                'first_paint_diff' => $firstPaintDiff.' s',
                'first_paint_diff_style' => $firstPaintDiffStyle.' s',
                'url' => substr($urlParsed['path'], 0, 127),
            ];
        }

        return $pageViewsPerformance;
    }

    /**
     * @return array
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function periodForUrl(string $url, \DateTime $start, \DateTime $end, array $metrics, DiagramOrchestrator $diagramOrchestrator)
    {
        $bucketizer = new Buckets(1, 15000);

        $period = [
            'from_date' => $start->format('Y-m-d'),
            'to_date' => $end->format('Y-m-d'),
        ];

        $samples = [];

        // Combine metric in array
        foreach ($metrics as $metric) {
            $requirements = [
                'periods' => [
                    $period,
                ],
                'technical_metrics' => [
                    $metric => 1,
                ],
                'filters' => [
                    'url' => [
                        'search_value' => $url,
                        'condition' => 'is',
                    ],
                    'device_type' => [
                        'search_value' => '1',
                        'condition' => 'is',
                    ],
                ],
            ];

            $collaboratorsAggregator = new CollaboratorsAggregator();
            $collaboratorsAggregator->fillRequirements($requirements);

            $diagramOrchestrator->load(
                $collaboratorsAggregator->getCollaborators()
            );

            $median = new Median();

            $technicalMetricsArr = $collaboratorsAggregator->getTechnicalMetrics()->getRequirements();

            $res = $diagramOrchestrator->process();

            $buckets = $bucketizer->bucketizePeriod(
                $res[0],
                $technicalMetricsArr[$metric]->getSelectDataFieldName()
            );

            $sampleDiagramValues = [];

            foreach ($buckets as $bucketSize => $bucket) {
                $sampleDiagramValues[$bucketSize] = \count($bucket);
            }

            $samples[$metric] = $median->calculateMedian(
                $sampleDiagramValues
            );
        }

        return $samples;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    private function tenMostPopularVisitedPages(string $date, int $offsetDays)
    {
        $excludeUrls = $this->getUrlIds(
            [
                'cart',
                'checkout',
                'customer',
                'sendung',
                'sales/',
            ]
        );

        $popularPages = [];

        $markerDate = new \DateTime($date);
        $futureDate = $markerDate->modify("+{$offsetDays} days");

        $markerDate = new \DateTime($date);

        $repository = $this->getDoctrine()->getRepository(NavigationTimings::class);

        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        $queryBuilder = $repository->createQueryBuilder('nt');

        $queryBuilder
            ->select(['count(nt.urlId) as visitsCount', 'nt.urlId'])
            ->where("nt.createdAt BETWEEN '".$markerDate->format('Y-m-d')." 00:00:00' AND '".$futureDate->format('Y-m-d')." 00:00:00'")
            ->andWhere('nt.urlId NOT IN('.implode(',', $excludeUrls).')')
            ->groupBy('nt.urlId')
            ->orderBy('count(nt.urlId)', 'DESC')
            ->setMaxResults(30)
            ->getQuery();

        $navigationTimings = $queryBuilder->getQuery()
            ->getResult();

        foreach ($navigationTimings as $nav) {
            $popularPages[$nav['urlId']] = $nav['visitsCount'];
        }

        arsort($popularPages);

        return \array_slice($popularPages, 0, 30, true);
    }

    /**
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function countViewsInPeriod(\DateTime $start, \DateTime $end)
    {
        $repository = $this->getDoctrine()->getRepository(NavigationTimings::class);

        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        $queryBuilder = $repository->createQueryBuilder('nt');

        $queryBuilder
            ->select(['count(nt.pageViewId) as visitsCount'])
            ->where("nt.createdAt BETWEEN '".$start->format('Y-m-d')." 00:00:00' AND '".$end->format('Y-m-d')." 00:00:00'")
            ->getQuery();

        return $queryBuilder->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array
     */
    public function getUrlIds(array $urls)
    {
        $repository = $this->getDoctrine()->getRepository(NavigationTimingsUrls::class);

        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        $queryBuilder = $repository->createQueryBuilder('nturl')
            ->select(['nturl.id']);

        foreach ($urls as $url) {
            $queryBuilder->orWhere("nturl.url LIKE '%".$url."%'");
        }

        $urlIds = $queryBuilder->getQuery()
            ->getArrayResult();

        return array_column($urlIds, 'id');
    }
}
