<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\BasicRum\Date\DayInterval;

class DiagramsGeneratorController extends Controller
{

    /**
     * @Route("/diagrams_generator/index", name="diagrams_generator_index")
     */
    public function index()
    {
        return $this->render('diagrams_generator/form.html.twig');
    }

    /**
     * @Route("/diagrams_generator/generate", name="diagrams_generator_generate")
     */
    public function generate()
    {
//        $response = new Response(
//            json_encode(
//                [
//                    'waterfall'             => $renderer->render($timings),
//                    'resource_distribution' =>
//                        [
//                            'labels' => array_keys($sizeDistribution),
//                            'values' => array_values($sizeDistribution)
//                        ],
//                    'user_agent'            => $navigationTiming[0]->getUserAgent()
//                ]
//            )
//        );

        $dayInterval = new DayInterval();

        $response = new Response(
            json_encode(
                [
                    'response' => print_r($dayInterval->generateDayIntervals($_POST['current_period_from_date'], $_POST['current_period_to_date']), true)
                ]
            )
        );

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}
