<?php

namespace App\Controller;

use PHPUnit\Runner\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\BasicRum\Boomerang\Builder;

class BrowserAgentController extends AbstractController
{

    /**
     * @Route("/browser/agent/builder", name="browser_agent_builder")
     */
    public function index()
    {
        $builder = new Builder();

        return $this->render('browser_agent/builder.html.twig',
            [
                'boomerang_plugins' => $builder->getAvailablePlugins()
            ]
        );
    }

    /**
     * @Route("/browser/agent/generate", name="browser_agent_generate")
     */
    public function generate()
    {
        $builder = new Builder();

        try {
            $result = $builder->build($_POST);
            $response = new Response(
                json_encode([
                    'error' => ''
                    ]
                )
            );
        } catch (\Exception $e) {
            $response = new Response(
                json_encode([
                        'error' => $e->getMessage()
                    ]
                )
            );
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
