<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\NavigationTimings;

use App\BasicRum\Report;
use App\BasicRum\DiagramBuilder;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index()
    {
        return $this->render('dashboard.html.twig',
            [
                'device_samples'  => json_encode($this->deviceSamples()),
                'last_page_views' => $this->lastPageViewsListHTML()
            ]
        );
    }

    /**
     * @return array
     */
    private function deviceSamples()
    {
        $devices = [
            'Desktop',
            'Tablet',
            'Mobile'
        ];

        $colors = [
            'Desktop' => 'rgb(31, 119, 180)',
            'Tablet'  => 'rgb(255, 127, 14)',
            'Mobile'  => 'rgb(44, 160, 44)'
        ];

        $today = new \DateTime(('-1 day'));
        $past  = new \DateTime('-2 weeks');

        $period = [
            'current_period_from_date' => $past->format('Y-m-d'),
            'current_period_to_date'   => $today->format('Y-m-d'),
        ];

        $samples = [];

        foreach ($devices as $device) {
            $data = [
                'period'      => $period,
                'perf_metric' => 'first_byte',
                'filters'     => [
                    'device_type' => [
                        'search_value' => $device,
                        'condition'    => 'is'
                    ]
                ]
            ];

            $report = new Report($this->getDoctrine());

            $diagramBuilder = new DiagramBuilder($report);

            $samples[$device] = $diagramBuilder->count($data);
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

        return $deviceDiagrams;
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
