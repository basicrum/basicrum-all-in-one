<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\VisitsOverview;
use App\Entity\NavigationTimingsUrls;
use App\Entity\NavigationTimings;

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
        $res = $this->_getTopLandingPages(10, 1, 8551494);

        $urls = [];

        foreach ($res as $urlId => $visits) {
            /** @var \App\Entity\NavigationTimingsUrls $navigationTimingUrl */
            $navigationTimingUrl = $this->getDoctrine()
                ->getRepository(NavigationTimingsUrls::class)
                ->findOneBy(['id' => $urlId]);

            $urls[$navigationTimingUrl->getId()] = $navigationTimingUrl->getUrl();
        }

        $metrics = [
            'time_to_first_byte' => 'First Byte',
            'time_to_first_paint' => 'Start Render'
        ];

        return $this->render('diagrams/pages_traffic.html.twig',
            [
                'urls'    => $urls,
                'metrics' => $metrics,
                'title'   => 'Performance - Landing Pages'
            ]
        );
    }

    /**
     * @Route("/diagrams/popular_pages_overtimeMedian", name="popular_pages_overtimeMedian")
     */
    public function popularPagesOvertime()
    {
        $res = $this->_getPopularPages(10, 1, 8551494);

        $urls = [];

        foreach ($res as $urlId => $visits) {
            /** @var \App\Entity\NavigationTimingsUrls $navigationTimingUrl */
            $navigationTimingUrl = $this->getDoctrine()
                ->getRepository(NavigationTimingsUrls::class)
                ->findOneBy(['id' => $urlId]);

            $urls[$navigationTimingUrl->getId()] = $navigationTimingUrl->getUrl();
        }

        $metrics = [
            'time_to_first_byte' => 'First Byte',
            'time_to_first_paint' => 'Start Render'
        ];

        return $this->render('diagrams/pages_traffic.html.twig',
            [
                'urls'    => $urls,
                'metrics' => $metrics,
                'title'   => 'Performance - Popular Pages'
            ]
        );
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
