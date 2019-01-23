<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\BasicRum\Report;
use App\BasicRum\DiagramBuilder;
use App\Entity\Releases;

use DateTime;

class DiagramsGeneratorController extends Controller
{

    /**
     * @Route("/diagrams_generator/index", name="diagrams_generator_index")
     */
    public function index()
    {
        $report = new Report($this->getDoctrine());

        $diagramBuilder = new DiagramBuilder($report);

        return $this->render('diagrams_generator/form.html.twig',
            [
                'navigation_timings' => $diagramBuilder->getNavigationTimings(),
                'page_types'         => $diagramBuilder->getPageTypes()
            ]
        );
    }

    /**
     * @Route("/diagrams_generator/generate", name="diagrams_generator_generate")
     */
    public function generate()
    {
        // Quick hack for out of memory problems
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ini_set('display_errors', '1');

        $periods = $_POST['period'];

        $diagrams = [];

        $shapes = [];

        $report = new Report($this->getDoctrine());

        $diagramBuilder = new DiagramBuilder($report);

        $colors = [
            0 => 'rgb(44, 160, 44)',
            1 => 'rgb(255, 127, 14)',
            2 => 'rgb(31, 119, 180)',
            3 => 'rgb(31, 119, 44)',
            4 => 'rgb(255, 119, 44)'
        ];

        foreach ($periods as $key => $period) {
            $data = [
                'period'      => $period,
                'perf_metric' => $_POST['perf_metric'],
                'filters'     => $_POST['filter'],
                'decorators'  => !empty($_POST['decorators']) ? $_POST['decorators'] : []
            ];

            $diagram = $diagramBuilder->build($data, (int) $_POST['bucket-size']);

            $medianX =  ($diagram['median']);
            $median = ($diagram['median'] / 1000) . ' sec';

            if (!empty($_POST['decorators']['show_median'])) {
                $shapes[] = [
                    'type' => 'line',
                    'x0'   => $medianX,
                    'y0'   => -0.5,
                    'x1'   => $medianX,
                    'y1'   => 7,
                    'line' => [
                        'color' => $colors[$key],
                        'width' =>  1.5,
                        'dash'  =>  'dot'
                    ]
                ];
            }

            $diagrams[] = array_merge(
                $diagram,
                [
                    'type' => 'line',
                    'line' => ['color' => $colors[$key]],
                    'name' => $period['current_period_from_date'] . ' - ' . $period['current_period_to_date'] . ' / median (' . $median . ')'
                ]
            );
        }

        $response = new Response(
            json_encode(
                [
                    'diagrams'            => $diagrams,
                    'layout_extra_shapes' => $shapes
                ]
            )
        );

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/diagrams_generator/overtimeMedian", name="diagrams_generator_overtimeMedian")
     */
    public function overtimeView()
    {
        // Quick hack for out of memory problems
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        ini_set('display_errors', '1');

        $pages = [
            'All Pages'      => '',
//            'Cart'     => '/checkout/cart',
//            'Product'  => '/catalog/product/view/id/',
//            'Checkout' => '/checkout/onepage'
        ];

        $pageDiagrams = [];

        foreach ($pages as $pageName => $url) {
            $res = $this->_pageOvertime($url);

            $pageDiagrams[] = [
                'diagrams'            => json_encode($res['diagrams']),
                'layout_extra_shapes' => json_encode($res['shapes']),
                'title'               => $pageName . " - First Paint (median)"
            ];
        }

        return $this->render('diagrams/over_time.html.twig',
            [
                'diagrams' => $pageDiagrams
            ]
        );
    }

    /**
     * @param string $url
     * @return array
     */
    private function _pageOvertime(string $url)
    {
        $today = new \DateTime();
        $past  = new \DateTime('-3 months');

        $periods =  [
            [
                'current_period_from_date' => $past->format('Y-m-d'),
                'current_period_to_date'   => $today->format('Y-m-d'),
            ]
        ];

        $deviceTypes = [
            'Desktop',
            'Tablet',
            'Mobile'
        ];

        $diagrams = [];

        $report = new Report($this->getDoctrine());

        $diagramBuilder = new DiagramBuilder($report);

        foreach ($periods as $period) {
            foreach ($deviceTypes as $device) {
                $data = [
                    'period'      => $period,
                    'perf_metric' => 'first_paint',
                    'filters'     => [
                        'device_type' => [
                            'search_value' => $device,
                            'condition'    => 'is'
                        ],
                        'url' => [
                            'search_value' => '',
                            'condition'    => 'contains'
                        ]
                    ]
                ];

                $diagram = $diagramBuilder->buildOverTime($data);

                $diagram = array_merge(
                    $diagram,
                    [
                        'type' => 'line',
                        'name' => $device
                    ]
                );

                $diagrams[] = $diagram;
            }
        }

        $repository = $this->getDoctrine()
            ->getRepository(Releases::class);

        $start = new DateTime($periods[0]['current_period_from_date']);
        $end   = new DateTime($periods[0]['current_period_to_date']);

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
            $releaseDates[] = $release->getDate();
            $shapes[] = [
                'type' => 'line',
                'x0'   => $release->getDate()->format('Y-m-d'),
                'y0'   => -0.5,
                'x1'   => $release->getDate()->format('Y-m-d'),
                'y1'   => 3000,
                'line' => [
                    'color' => '#ccc',
                    'width' =>  2,
                    'dash'  =>  'dot'
                ]
            ];

            $releaseAnnotations['x'][]    = $release->getDate()->format('Y-m-d');
            $releaseAnnotations['y'][]    = 3000;
            $releaseAnnotations['text'][] = $release->getDescription();
        }

        $diagrams[] = $releaseAnnotations;

        return [
            'diagrams' => $diagrams,
            'shapes'   => $shapes
        ];
    }

}
