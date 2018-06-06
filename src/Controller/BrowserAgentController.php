<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BrowserAgentController extends Controller
{

    /**
     * @Route("/browser/agent/builder", name="browser_agent_builder")
     */
    public function index()
    {
        $boomerangPlugins = [
            'navigation_timings'     => 'Navigation Timings',
            'resource_timings'       => 'Resource Timings',
            'first_contentful_paint' => 'First Contentful Paint',
            'js_error_reporting'     => 'JS Error reporting',
            'continuity'             => 'Continuity',
        ];

        return $this->render('browser_agent/builder.html.twig', ['boomerang_plugins' => $boomerangPlugins]);
    }

}
