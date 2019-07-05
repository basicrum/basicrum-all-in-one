<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\BasicRum\CollaboratorsAggregator;
use App\BasicRum\DiagramOrchestrator;
use App\Entity\NavigationTimings;

use App\BasicRum\Layers\Presentation;

class WaterfallsController extends AbstractController
{

    private $_deviceMapping = [
        '2' => 'Desktop',
        '3' => 'Tablet',
        '1' => 'Mobile',
        '4' => 'Bot',
        '5' => 'Unknown'
    ];

    /**
     * @Route("/waterfalls/index", name="waterfalls_index")
     */
    public function index()
    {
        $presentation = new Presentation();

        return $this->render('waterfalls/form.html.twig',
            [
                'navigation_timings' => $presentation->getTechnicalMetricsSelectValues(),
                'operating_systems'  => $presentation->getOperatingSystemSelectValues($this->getDoctrine()),
                'page_types'         => $presentation->getPageTypes($this->getDoctrine())
            ]
        );
    }

    /**
     * @Route("/waterfalls/list", name="waterfalls_list")
     */
    public function generate()
    {
        // Quick hack for out of memory problems
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $collaboratorsAggregator = new CollaboratorsAggregator();

        $requirements = [];

        /**
         * Ugly filtering of post data in order to map form data correctly to dataLayer API
         */
        foreach ($_POST as $keyO => $data) {
            if (is_string($data) && strpos($data, '|') !== false) {
                $e = explode('|', $data);
                $requirements[$keyO] = [$e[0] => $e[1]];

                continue;
            }

            $requirements[$keyO] = $data;
        }


        /**
         * If "page_type" presented then unset "url" and "query_param".
         */
        if ( !empty($requirements['filters']['page_type']) ) {
            $pageTypeId = $requirements['filters']['page_type'];

            $repository = $this->getDoctrine()->getRepository(PageTypeConfig::class);

            /** @var PageTypeConfig $pageType */
            $pageType = $repository->find($pageTypeId);

            $requirements['filters']['url'] = [
                'condition'    => $pageType->getConditionValue(),
                'search_value' => $pageType->getConditionTerm()
            ];

            unset($requirements['filters']['page_type']);
            unset($requirements['filters']['query_param']);
        }

        $collaboratorsAggregator->fillRequirements($requirements);

        $diagramOrchestrator = new DiagramOrchestrator(
            $collaboratorsAggregator->getCollaborators(),
            $this->getDoctrine()
        );

        $res = $diagramOrchestrator->process();

        foreach ($res[0] as $key => $day) {
            if(empty($day)) {
                unset($res[0][$key]);
            }
        }

        $reversedDays = array_reverse($res[0]);

        $pageViews = [];

        $counter = 0;

        $repository = $this->getDoctrine()
            ->getRepository(NavigationTimings::class);

        foreach ($reversedDays as $day => $views) {
            foreach ($views as $view) {
                $pageViews[] = $repository->find($view['pageViewId']);
                $counter++;
                if ($counter === 400) {
                    break;
                }
            }
            break;
        }

        return $this->render('waterfalls/waterfalls_table.html.twig',
            [
                'page_views'     => $pageViews,
                'device_mapping' => $this->_deviceMapping
            ]
        );
    }

}
