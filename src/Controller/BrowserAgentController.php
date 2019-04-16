<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BrowserAgentController extends AbstractController
{

    /**
     * @Route("/browser/agent/builder", name="browser_agent_builder")
     */
    public function index()
    {
        $boomerangPlugins = [
            'navigation_timings'  => 'Navigation Timings',
            'resource_timings'    => 'Resource Timings',
            'paint_timings'       => 'Paint Timings',
            'network_information' => 'First Contentful Paint'
        ];

        return $this->render('browser_agent/builder.html.twig',
            [
                'boomerang_plugins' => $boomerangPlugins
            ]
        );
    }

    /**
     * @Route("/browser/agent/generate", name="browser_agent_generate")
     */
    public function generate()
    {
        $response = new Response(print_r($_POST, true));

        //$response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
