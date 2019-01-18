<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\NavigationTimings;

use App\BasicRum\Date\DayInterval;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index()
    {
        $today = new \DateTime();
        $past  = new \DateTime('-3 months');

        $dayInterval = new DayInterval();

        $interval = $dayInterval->generateDayIntervals(
            $past->format('Y-m-d'),
            $today->format('Y-m-d')
        );

        $samples = [
            'Desktop' => [],
            'Tablet'  => [],
            'Mobile'  => []
        ];

        $colors = [
            'Desktop' => 'rgb(31, 119, 180)',
            'Tablet'  => 'rgb(255, 127, 14)',
            'Mobile'  => 'rgb(44, 160, 44)'
        ];

        $growthCounter = 0;

        foreach ($interval as $period) {
            foreach ($samples as $deviceType => $data) {
                $count = 0;

                if ($deviceType === 'Desktop') {
                    $count = rand(20000, 26000);
                }

                if ($deviceType === 'Tablet') {
                    $count = rand(7000, 7500);
                }

                if ($deviceType === 'Mobile') {
                    $count = rand(23000, 28000);
                }

                $samples[$deviceType][$period['start']] = $count + $growthCounter * rand(10, 25);

                $growthCounter++;
            }
        }

        $deviceDiagrams = [];

        foreach ($samples as $device => $data) {
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

        return $this->render('dashboard.html.twig',
            [
                'device_samples'  => json_encode($deviceDiagrams),
                'last_page_views' => $this->lastPageViewsListHTML()

            ]
        );
    }

    private function lastPageViewsListHTML()
    {
        $repository = $this->getDoctrine()
            ->getRepository(NavigationTimings::class);
        $query = $repository->createQueryBuilder('nt')
            //->where("nt.url LIKE '%GOO%'")
            //->setParameter('url', 'GOO')
            ->orderBy('nt.pageViewId', 'DESC')
            ->setMaxResults(400)
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
                'page_views' => $navTimingsFiltered
            ]
        );
    }

}
