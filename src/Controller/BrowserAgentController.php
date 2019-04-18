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

        $buildsListHtml = $this->get('twig')->render(
            'browser_agent/builds_list.html.twig',
            [
                'builds' => $builder->getAllBuilds($this->getDoctrine())
            ]
        );

        $builderHtml = $this->get('twig')->render(
            'browser_agent/builder.html.twig',
            [
                'boomerang_plugins' => $builder->getAvailablePlugins()
            ]
        );

        $iframeIncludeSnippet = $this->get('twig')->render(
            'browser_agent/include_snippets/iframe.html.twig'
        );

        $mainDocumentIncludeSnippet = $this->get('twig')->render(
            'browser_agent/include_snippets/main_document.html.twig'
        );

        return $this->render('browser_agent/index.html.twig',
            [
                'buildsListHtml'             => $buildsListHtml,
                'builderHtml'                => $builderHtml,
                'iframeIncludeSnippet'       => $iframeIncludeSnippet,
                'mainDocumentIncludeSnippet' => $mainDocumentIncludeSnippet
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
            $result = $builder->build($_POST, $this->getDoctrine());
            $response = new Response(
                json_encode([
                        'error'    => '',
                        'build_id' => $result
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

    /**
     * @Route("/browser/agent/builds_list", name="browser_agent_builds_list")
     */
    public function buildsList()
    {
        $builder = new Builder();

        return $this->render(
            'browser_agent/builds_list.html.twig',
            [
                'builds' => $builder->getAllBuilds($this->getDoctrine())
            ]
        );
    }

    /**
     * @Route("/browser/agent/builds_show", name="browser_agent_builds_show")
     */
    public function showBuild()
    {
        $builder = new Builder();

        $buildId = $_GET['build_id'];

        return $this->render(
            'browser_agent/build_info.html.twig',
            $builder->getBuildInfo($buildId, $this->getDoctrine())
        );
    }

}
