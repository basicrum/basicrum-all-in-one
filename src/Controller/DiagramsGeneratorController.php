<?php

declare(strict_types=1);

namespace App\Controller;

use App\BasicRum\Date\TimePeriod;
use App\BasicRum\DiagramBuilder;
use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Layers\Presentation;
use App\Entity\PageTypeConfig;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiagramsGeneratorController extends AbstractController
{
    /**
     * @Route("/diagrams_generator/index", name="diagrams_generator_index")
     */
    public function index()
    {
        $presentation = new Presentation();

        $timePeriod = new TimePeriod();
        $period = $timePeriod->getPastDaysFromNow(30);

        return $this->render('diagrams_generator/form.html.twig',
            [
                'navigation_timings' => $presentation->getTechnicalMetricsSelectValues(),
                'operating_systems' => $presentation->getOperatingSystemSelectValues($this->getDoctrine()),
                'page_types' => $presentation->getPageTypes($this->getDoctrine()),
                'period' => $period,
            ]
        );
    }

    /**
     * @Route("/diagrams_generator/generate", name="diagrams_generator_generate")
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function generate(DiagramOrchestrator $diagramOrchestrator)
    {
        // Quick hack for out of memory problems
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $requirements = [];

        $requirements['global'] = $_POST['global'];
        $requirements['segments'] = [];
        /*
         * Ugly filtering of post data in order to map form data correctly to diagram APIs
         */
        foreach ($_POST['segments'] as $keyO => $data) {
            //var_dump($data['data_requirements']['technical_metrics']);
            $requirements['segments'][$keyO] = $data;

            if (\is_string($data['data_requirements']['technical_metrics']) && false !== strpos($data['data_requirements']['technical_metrics'], '|')) {
                $e = explode('|', $data['data_requirements']['technical_metrics']);
                $requirements['segments'][$keyO]['data_requirements']['technical_metrics'] = [$e[0] => $e[1]];

                continue;
            }
        }

        /*
         * If "page_type" presented then unset "url" and "query_param".
         */
        if (!empty($requirements['filters']['page_type'])) {
            $pageTypeId = $requirements['filters']['page_type'];

            $repository = $this->getDoctrine()->getRepository(PageTypeConfig::class);

            /** @var PageTypeConfig $pageType */
            $pageType = $repository->find($pageTypeId);

            $requirements['filters']['url'] = [
                'condition' => $pageType->getConditionValue(),
                'search_value' => $pageType->getConditionTerm(),
            ];

            unset($requirements['filters']['page_type']);
            unset($requirements['filters']['query_param']);
        }

        $diagramBuilder = new DiagramBuilder();
        $diagramOrchestrator->load($requirements);

        $data = $diagramBuilder->build($diagramOrchestrator, $requirements);

        return $this->json($data);
    }
}
