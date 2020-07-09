<?php

namespace App\Controller;

use App\BasicRum\Boomerang\Builder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
                'builds' => $builder->getAllBuilds($this->getDoctrine()),
            ]
        );

        $builderHtml = $this->get('twig')->render(
            'browser_agent/builder.html.twig',
            [
                'boomerang_plugins' => $builder->getAvailablePlugins(),
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
                'buildsListHtml' => $buildsListHtml,
                'builderHtml' => $builderHtml,
                'iframeIncludeSnippet' => $iframeIncludeSnippet,
                'mainDocumentIncludeSnippet' => $mainDocumentIncludeSnippet,
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

            $response = [
                'error' => '',
                'build_id' => $result,
            ];
        } catch (\Exception $e) {
            $response = [
                'error' => $e->getMessage(),
            ];
        }

        $this->json($response);
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
                'builds' => $builder->getAllBuilds($this->getDoctrine()),
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

        $infoBlockHtml = $this->get('twig')->render(
            'browser_agent/build_info.html.twig',
            $builder->getBuildInfo($buildId, $this->getDoctrine())
        );

        $build = $builder->getBuild($buildId, $this->getDoctrine());

        return $this->json([
            'info' => $infoBlockHtml,
            'build_id' => $buildId,
            'build_date' => $build->getCreatedAt()->format('F d, Y'),
        ]);
    }

    /**
     * @Route("/browser/agent/download", name="browser_agent_download")
     */
    public function download()
    {
        $buildId = $_GET['build_id'];

        $builder = new Builder();

        $build = $builder->getBuild($buildId, $this->getDoctrine());

        $boomerangVersion = $build->getBoomerangVersion();
        $fileName = str_replace('|', '-', $boomerangVersion);
        $fileName .= '-boomerang.js';

        $response = new Response($build->getBuildResult());

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $fileName
        );

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
