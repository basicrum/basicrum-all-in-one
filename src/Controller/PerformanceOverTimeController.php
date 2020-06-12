<?php

declare(strict_types=1);

namespace App\Controller;

use App\BasicRum\Date\DayInterval;
use App\BasicRum\Date\TimePeriod;
use App\Entity\NavigationTimingsUrls;
use App\Entity\RumDataFlat;
use App\Entity\VisitsOverview;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PerformanceOverTimeController extends AbstractController
{
    /**
     * @Route("/diagrams/allTraffic", name="diagrams_all_traffic")
     */
    public function allTraffic()
    {
        return $this->render('diagrams/all_traffic.html.twig');
    }

    /**
     * @Route("/diagrams/landing_pages_overtimeMedian", name="landing_pages_overtimeMedian")
     */
    public function landingPagesOvertime()
    {
        $timePeriod = new TimePeriod();
        $period = $timePeriod->getPastDaysFromNow(30);

        $dayInterval = new DayInterval();
        $interval = $dayInterval->generateDayIntervals($period->getStart(), $period->getEnd());

        $res = $this->_getTopLandingPages(10, $interval);

        $urls = [];

        foreach ($res as $urlId => $visits) {
            /** @var \App\Entity\NavigationTimingsUrls $navigationTimingUrl */
            $navigationTimingUrl = $this->getDoctrine()
                ->getRepository(NavigationTimingsUrls::class)
                ->findOneBy(['id' => $urlId]);

            $urls[$navigationTimingUrl->getId()] = $navigationTimingUrl->getUrl();
        }

        $metrics = [
            'first_byte' => 'First Byte',
            'first_paint' => 'Start Render',
        ];

        return $this->render('diagrams/pages_traffic.html.twig',
            [
                'urls' => $urls,
                'metrics' => $metrics,
                'title' => 'Performance - Landing Pages',
                'landing_pages' => 1,
            ]
        );
    }

    /**
     * @Route("/diagrams/popular_pages_overtimeMedian", name="popular_pages_overtimeMedian")
     */
    public function popularPagesOvertime()
    {
        $timePeriod = new TimePeriod();
        $period = $timePeriod->getPastDaysFromNow(30);

        $dayInterval = new DayInterval();
        $interval = $dayInterval->generateDayIntervals($period->getStart(), $period->getEnd());

        $res = $this->_getPopularPages(10, $interval);

        $urls = [];

        foreach ($res as $urlId => $visits) {
            /** @var \App\Entity\NavigationTimingsUrls $navigationTimingUrl */
            $navigationTimingUrl = $this->getDoctrine()
                ->getRepository(NavigationTimingsUrls::class)
                ->findOneBy(['id' => $urlId]);

            $urls[$navigationTimingUrl->getId()] = $navigationTimingUrl->getUrl();
        }

        $metrics = [
            'first_byte' => 'First Byte',
            'first_paint' => 'Start Render',
        ];

        return $this->render('diagrams/pages_traffic.html.twig',
            [
                'urls' => $urls,
                'metrics' => $metrics,
                'title' => 'Performance - Popular Pages',
                'landing_pages' => 0,
            ]
        );
    }

    /**
     * @return array
     */
    private function _getTopLandingPages(int $count, array $interval)
    {
        $begin = current($interval)['start'];
        $end = end($interval)['end'];

        $minId = 0;
        $maxId = 0;

        /** @var NavigationTimings $lastNavigationTiming */
        $lastNavigationTiming = $this->getDoctrine()
            ->getManager()
            ->createQueryBuilder()
            ->select('e')
            ->where('e.createdAt BETWEEN :begin AND :end')
            ->setParameter('begin', $begin)
            ->setParameter('end', $end)
            ->from(RumDataFlat::class, 'e')
            ->orderBy('e.pageViewId', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (null !== $lastNavigationTiming) {
            $maxId = $lastNavigationTiming->getPageViewId();
        }

        /** @var NavigationTimings $lastNavigationTiming */
        $firstNavigationTiming = $this->getDoctrine()
            ->getManager()
            ->createQueryBuilder()
            ->select('e')
            ->where('e.createdAt BETWEEN :begin AND :end')
            ->setParameter('begin', $begin)
            ->setParameter('end', $end)
            ->from(RumDataFlat::class, 'e')
            ->orderBy('e.pageViewId', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (null !== $firstNavigationTiming) {
            $minId = $firstNavigationTiming->getPageViewId();
        }

        $repository = $this->getDoctrine()->getRepository(VisitsOverview::class);

        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        $queryBuilder = $repository->createQueryBuilder('vo');

        $queryBuilder
            ->select(['count(vo.firstUrlId) as visitsCount', 'vo.firstUrlId'])
            ->where('vo.firstPageViewId BETWEEN '.$minId.' AND '.$maxId)
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

    /**
     * @return array
     */
    private function _getPopularPages(int $count, array $interval)
    {
        $repository = $this->getDoctrine()->getRepository(RumDataFlat::class);

        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        $queryBuilder = $repository->createQueryBuilder('nt');

        $begin = current($interval)['start'];
        $end = end($interval)['end'];

        $queryBuilder
            ->select(['count(nt.urlId) as visitsCount', 'nt.urlId'])
            ->where('nt.createdAt BETWEEN :begin AND :end')
            ->setParameter('begin', $begin)
            ->setParameter('end', $end)
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
