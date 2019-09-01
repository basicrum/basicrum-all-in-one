<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\BasicRum\DiagramBuilder;
use App\BasicRum\DiagramOrchestrator;

class WidgetController extends AbstractController
{

    /**
     * @Route("/widget/generate_diagram", name="widget_generate_diagram")
     */
    public function generateDiagram()
    {
        ini_set('memory_limit', '-1');

        $diagramOrchestrator = new DiagramOrchestrator(
            $_POST,
            $this->getDoctrine()
        );

        $diagramBuilder = new DiagramBuilder();

        $data = $diagramBuilder->build($diagramOrchestrator, $_POST);

        $response = new Response(
            json_encode(
                $data
            )
        );

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}
