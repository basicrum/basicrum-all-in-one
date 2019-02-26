<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Releases;

use App\BasicRum\CollaboratorsAggregator;
use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Buckets;
use App\BasicRum\DiagramBuilder;

use App\BasicRum\Layers\Presentation;

use DateTime;

class DiagramsGeneratorController extends AbstractController
{

    /**
     * @Route("/diagrams_generator/index", name="diagrams_generator_index")
     */
    public function index()
    {
        $presentation = new Presentation();

        return $this->render('diagrams_generator/form.html.twig',
            [
                'navigation_timings' => $presentation->getTechnicalMetricsSelectValues(),
                'page_types'         => []
            ]
        );
    }

    /**
     * @Route("/diagrams_generator/generate", name="diagrams_generator_generate")
     */
    public function generate()
    {
        $collaboratorsAggregator = new CollaboratorsAggregator();

        $requirements = [];

        /**
         * Ugly filtering of post data in order to map form data correctly to dataLayer API
         */
        foreach ($_POST as $keyO => $data) {
            if (is_string($data) && strpos($data, '|') !== false) {
                $e = explode('|', $data);
                $requirements[$keyO] = [$e[0] => $e[1]];

                continue;
            }

            $requirements[$keyO] = $data;
        }

        $requirements['business_metrics']['stay_on_page_time'] = 1;

        $collaboratorsAggregator->fillRequirements($requirements);

        $diagramOrchestrator = new DiagramOrchestrator(
            $collaboratorsAggregator->getCollaborators(),
            $this->getDoctrine()
        );

        $res = $diagramOrchestrator->process();

        $usedTechnicalMetrics = $collaboratorsAggregator->getTechnicalMetrics()->getRequirements();
        $technicalMetricName = reset($usedTechnicalMetrics)->getSelectDataFieldName();

        $upperLimit = 5000;

        if ($technicalMetricName === 'loadEventEnd') {
            $upperLimit = 12000;
        }

        $bucketSize = (int) $requirements['visualize']['bucket'];

        $bucketizer = new Buckets($bucketSize, $upperLimit);
        $buckets = $bucketizer->bucketizePeriod($res[0], $technicalMetricName);

        $builder = new DiagramBuilder();

        $response = new Response(
            json_encode(
                $builder->build($buckets, $collaboratorsAggregator)
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
