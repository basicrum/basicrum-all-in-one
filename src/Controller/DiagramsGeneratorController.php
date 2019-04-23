<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Releases;
use App\Entity\PageTypeConfig;

use App\BasicRum\CollaboratorsAggregator;
use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Buckets;
use App\BasicRum\Statistics\Median;
use App\BasicRum\DiagramBuilder;

use App\BasicRum\Layers\Presentation;

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
                'operating_systems'  => $presentation->getOperatingSystemSelectValues($this->getDoctrine()),
                'page_types'         => $presentation->getPageTypes($this->getDoctrine())
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


        /**
         * If "page_type" presented then unset "url" and "query_param".
         */
        if ( !empty($requirements['filters']['page_type']) ) {
            $pageTypeId = $requirements['filters']['page_type'];

            $repository = $this->getDoctrine()->getRepository(PageTypeConfig::class);

            /** @var PageTypeConfig $pageType */
            $pageType = $repository->find($pageTypeId);

            $requirements['filters']['url'] = [
                'condition'    => $pageType->getConditionValue(),
                'search_value' => $pageType->getConditionTerm()
            ];

            unset($requirements['filters']['page_type']);
            unset($requirements['filters']['query_param']);
        }

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

            $maxYaxis = $res['ymax'] + 1000;
            $maxYaxis = 1000 * ((int) ($maxYaxis / 1000));

            $pageDiagrams[] = [
                'section_title'       => 'All Pages',
                'diagrams'            => json_encode($res['diagrams']),
                'layout_extra_shapes' => json_encode($res['shapes']),
                'title'               => $pageName . " - First Paint (median)",
                'layout_overrides'    => json_encode(
                    [
                        'yaxis' => [
                            'range' => [0, $maxYaxis],
                            'tickvals' => [1000, 2000, 3000, 4000, 5000],
                            'ticktext' =>  ["1 sec", "2 sec", "3 sec", "4 sec", "5 sec"],
                            'fixedrange' => true
                        ]
                    ]
                )
            ];

            $bounceRateDiagrams[] = json_encode($res['bounce_rate_diagrams']);
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
        $past  = new \DateTime('04/07/2019');
        $today = new \DateTime('04/12/2019');

        $bucketizer = new Buckets(1, 10000);
        $median = new Median();

        $deviceTypes = [
            2 => 'Desktop',
            3 => 'Tablet',
            1 => 'Mobile'
        ];

        $diagramsByType = [];

        foreach ($deviceTypes as $deviceType) {
            $diagramsByType[$deviceType] = [];
        }

        $bounceRateDiagrams = [];

        foreach ($deviceTypes as $deviceId => $device) {
            $requirementsArr = [
                'filters' => [
                    'device_type' => [
                        'condition'    => 'is',
                        'search_value' =>  (string) $deviceId
                    ]
                ],
                'periods' => [
                    [
                        'from_date' => $past->format('Y-m-d'),
                        'to_date'   => $today->format('Y-m-d')
                    ]
                ],
                'technical_metrics' => [
                    'time_to_first_paint' => 1
                ],
//                'business_metrics'  => [
//                    'bounce_rate'       => 1
//                ]
            ];

            $collaboratorsAggregator = new CollaboratorsAggregator();

            $collaboratorsAggregator->fillRequirements($requirementsArr);

            $diagramOrchestrator = new DiagramOrchestrator(
                $collaboratorsAggregator->getCollaborators(),
                $this->getDoctrine()
            );

            $res = $diagramOrchestrator->process();

            foreach ($res as $daySamples) {
                foreach ($daySamples as $day => $samples) {
                    $buckets = $bucketizer->bucketize($samples, 'firstPaint');
                    $sampleDiagramValues = [];

                    foreach ($buckets as $bucketSize => $bucket) {
                        $sampleDiagramValues[$bucketSize] = count($bucket);
                    }

                    $diagramsByType[$device][$day] = $median->calculateMedian($sampleDiagramValues);
                }
            }
        }

        $maxYValues = [];

        foreach ($diagramsByType as $device => $data) {
            $diagrams[] = [
                'name' => $device,
                'type' => 'line',
                'x'    => array_keys($data),
                'y'    => array_values($data)
            ];

            $yValues = array_values($data);

            $maxYValues[] = max($yValues);
        }

        $repository = $this->getDoctrine()
            ->getRepository(Releases::class);

        $start = $past;
        $end   = $today;

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
            'bounce_rate_diagrams' => $bounceRateDiagrams,
            'ymax' => max($maxYValues)
        ];
    }

}
