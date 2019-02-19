<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\BasicRum\DiagramOrchestrator;

use App\BasicRum\BounceRate\Calculator;

class BounceRateController extends AbstractController
{


    /**
     * @Route("/diagrams/bounce_rate/calculate", name="diagrams_bounce_rate_calculate")
     */
    public function calculate()
    {
        // Quick hack for out of memory problems
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $calculator = new Calculator($this->getDoctrine());
        $calculator->calculate();

        $response = new Response(
            json_encode(
                [
                    'test' => 'test'
                ]
            )
        );

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/diagrams/bounce_rate/distribution", name="diagrams_bounce_rate_distribution")
     */
    public function firstPaintDistribution()
    {
        // Quick hack for out of memory problems
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $diagramOrchestrator = new DiagramOrchestrator($this->getDoctrine());

        $requirementsArr = [
            'filters' => [
                'device_type' => [
                    'condition'    => 'is',
                    'search_value' => 'mobile'
                ],
                'device_manufacturer' => [
                    'condition'    => 'is',
                    'search_value' => 'Huawei'
                ],
                'browser_name' => [
                    'condition'    => 'is',
                    'search_value' => 'Chrome Dev'
                ]
            ],
            'periods' => [
                [
                    'from_date' => '02/01/2019',
                    'to_date'   => '02/07/2019'
                ]
            ],
            'technical_metrics' => [
                'time_to_first_paint' => 1
            ],
            'business_metrics'  => [
                'bounce_rate' => 1
            ]
        ];

        $diagramOrchestrator->fillRequirements($requirementsArr);

        $res = $diagramOrchestrator->process();

        $sessionsCount = 0;
        $bouncesCount = 0;
        $convertedSessions = 0;

        $groupMultiplier = 200;
        $upperLimit = 5000;

        $firstPaintArr = [];
        $allFirstPaintArr = [];
        $bouncesGroup  = [];
        $bouncesPercents = [];

        // Init the groups/buckets
        for($i = $groupMultiplier; $i <= $upperLimit; $i += $groupMultiplier) {
            $allFirstPaintArr[$i] = 0;
        }

        for($i = $groupMultiplier; $i <= $upperLimit; $i += $groupMultiplier) {
            $firstPaintArr[$i] = 0;
            $allFirstPaintArr[$i] = 0;
            if ($i >= 250 && $i <= $upperLimit) {
                $bouncesGroup[$i] = 0;
            }
        }

        foreach ($res[0] as $day) {
            foreach ($day as $row) {
                $ttfp  = $row['firstPaint'];

                $paintGroup = $groupMultiplier * (int) ($ttfp / $groupMultiplier);

                if ($upperLimit >= $paintGroup && $paintGroup > 0) {
                    $allFirstPaintArr[$paintGroup]++;
                }

                if ($upperLimit >= $paintGroup && $paintGroup > 0) {

                    if ($paintGroup >= 250 && $paintGroup  <= $upperLimit) {
                        $firstPaintArr[$paintGroup]++;
                        $sessionsCount++;

                        if ($row['pageViewsCount'] == 1) {
                            $bouncesCount++;
                            $bouncesGroup[$paintGroup]++;
                        }
                    }
                }
            }
        }

        $xAxisLabels = [];

        foreach($firstPaintArr as $paintGroup => $numberOfProbes) {
            $time = ($paintGroup / 1000);

            $xAxisLabels[] = $time;

            if ($numberOfProbes > 0) {
                if ($paintGroup >= 250 && $paintGroup <= $upperLimit) {
                    $bouncesPercents[$paintGroup] = (int) number_format(($bouncesGroup[$paintGroup] / $numberOfProbes) * 100);
                }
            }
        }

        return $this->render('diagrams/bounce_first_paint.html.twig',
            [
                'count'             => $sessionsCount,
                'bounceRate'        => (int) number_format(($bouncesCount / $sessionsCount) * 100),
                'conversionRate'    => (int) number_format(($convertedSessions / $sessionsCount) * 100),
                'x1Values'          => json_encode(array_keys($firstPaintArr)),
                'y1Values'          => json_encode(array_values($firstPaintArr)),
                'x2Values'          => json_encode(array_keys($bouncesPercents)),
                'y2Values'          => json_encode(array_values($bouncesPercents)),
                'annotations'       => json_encode($bouncesPercents),
                'x_axis_labels'     => json_encode(array_values($xAxisLabels)),
                'startDate'         => 'none',
                'endDate'           => 'none'
            ]
        );
    }

}
