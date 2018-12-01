<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\BasicRum\Report;
use App\BasicRum\DiagramBuilder;

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
                'navigation_timings' => $diagramBuilder->getNavigationTimings()
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
            2 => 'rgb(31, 119, 180)'
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

        $periods =  [
                        [
                            'current_period_from_date' => '09/03/2018',
                            'current_period_to_date'   => '09/04/2018',
                        ]
                    ];

        $diagrams = [];

        $report = new Report($this->getDoctrine());

        $diagramBuilder = new DiagramBuilder($report);

        foreach ($periods as $period) {
            $data = [
                'period'      => $period,
                'perf_metric' => 'nt_first_paint'
            ];

            $diagram = $diagramBuilder->buildOverTime($data);

            $diagrams[] = array_merge(
                $diagram,
                [
                    'type' => 'line'
                ]
            );
        }

        return $this->render('diagrams/over_time.html.twig',
            [
                'diagrams' => json_encode($diagrams)
            ]
        );
    }

}