<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\BasicRum\DiagramBuilder;

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
        $diagramBuilder = new DiagramBuilder();

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
