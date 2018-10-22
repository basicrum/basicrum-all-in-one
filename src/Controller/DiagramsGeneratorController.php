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

        $report = new Report($this->getDoctrine());

        $diagramBuilder = new DiagramBuilder($report);

        $response = new Response(
            json_encode(
                [
                    'response' => print_r($diagramBuilder->build($_POST), true)
                ]
            )
        );

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}
