<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\BasicRum\Report;
use App\BasicRum\DiagramBuilder;
use App\Entity\Releases;

use App\BasicRum\DiagramOrchestrator;

use DateTime;

class DiagramsGeneratorController extends AbstractController
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

    private function _getTestData()
    {
        $data =
<<<EOT
{
  "technical_metrics": {
    "first_paint": "1"
  },
  "visualize": {
    "bucket_size": "100",
    "time_range": "5000"
  },
  "filters": {
    "device_type": {
        "condition": "is",
      "search_value": ""
    },
    "os_name": {
        "condition": "is",
      "search_value": ""
    },
    "browser_name": {
        "condition": "is",
      "search_value": ""
    },
    "url": {
        "condition": "contains",
      "search_value": ""
    },
    "page_type": {
      "search_value": "",
      "condition": "contains"
    }
  },
  "periods": [
    {
        "current_period_from_date": "10/24/2018",
      "current_period_to_date": "10/24/2018"
    },
    {
        "current_period_from_date": "10/16/2018",
      "current_period_to_date": "10/17/2018"
    },
    {
        "current_period_from_date": "09/30/2018",
      "current_period_to_date": "09/30/2018"
    },
    {
        "current_period_from_date": "12/09/2018",
      "current_period_to_date": "12/09/2018"
    },
    {
        "current_period_from_date": "01/04/2019",
      "current_period_to_date": "01/20/2019"
    }
  ],
  "decorators": {
    "density": "1",
    "show_median": "1"
  },
  "business_metrics": {
    "bounce_rate": "1"
  }
}
EOT;

        $filtersData =
<<<EOT
{
  "filters": {
    "device_type": {
        "condition": "is",
      "search_value": "mobile"
    },
    "os_name": {
        "condition": "is",
      "search_value": ""
    },
    "browser_name": {
        "condition": "is",
      "search_value": ""
    },
    "url": {
        "condition": "contains",
      "search_value": ""
    },
    "page_type": {
      "search_value": "",
      "condition": "contains"
    }
  }
}
EOT;


        return $filtersData;
    }

    /**
     * @Route("/diagrams_generator/generate_clean", name="diagrams_generator_generate_clean")
     */
    public function generateClean()
    {
        $test = new DiagramOrchestrator($this->getDoctrine());

        $reqs = json_decode($this->_getTestData(), true);

        // Test only for filters
        $test->fillRequirements($reqs);
        $test->process();

        return new Response(json_encode($_POST));
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

        $pageDiagrams       = [];
        $bounceRateDiagrams = [];

        foreach ($pages as $pageName => $url) {
            $res = $this->_pageOvertime($url);

            $pageDiagrams[] = [
                'diagrams'            => json_encode($res['diagrams']),
                'layout_extra_shapes' => json_encode($res['shapes']),
                'title'               => $pageName . " - First Paint (median)"
            ];

            $bounceRateDiagrams[] =json_encode($res['bounce_rate_diagrams']);
        }

        return $this->render('diagrams/over_time.html.twig',
            [
                'diagrams'             => $pageDiagrams,
                'bounce_rate_diagrams' => $bounceRateDiagrams
            ]
        );
    }

    /**
     * @param string $url
     * @return array
     */
    private function _pageOvertime(string $url)
    {
        $today = new \DateTime('-1 day');
        $past  = new \DateTime('-3 days');

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

        $bounceRateDiagrams = [];

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

                $report = $diagramBuilder->buildOverTime($data);

                $diagram = $report['performance'];

                $bounceRateDiagram = $report['bounce_rate'];

                $diagram = array_merge(
                    $diagram,
                    [
                        'type' => 'line',
                        'name' => $device
                    ]
                );


                $bounceRateDiagram = array_merge(
                    $bounceRateDiagram,
                    [
                        'type' => 'line',
                        'name' => $device
                    ]
                );

                $diagrams[] = $diagram;
                $bounceRateDiagrams[] = $bounceRateDiagram;
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
            'shapes'   => $shapes,
            'bounce_rate_diagrams' => $bounceRateDiagrams
        ];
    }

}
