<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\BasicRum\Date\TimePeriod;
use App\BasicRum\DiagramOrchestrator;
use App\Entity\NavigationTimings;
use App\Entity\PageTypeConfig;

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

        $timePeriod = new TimePeriod();
        $period = $timePeriod->getPastDaysFromNow(30);

        return $this->render('waterfalls/form.html.twig',
            [
                'navigation_timings' => $presentation->getTechnicalMetricsSelectValues(),
                'operating_systems'  => $presentation->getOperatingSystemSelectValues($this->getDoctrine()),
                'page_types'         => $presentation->getPageTypes($this->getDoctrine()),
                'period'             => $period
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

        $requirements = [];

        $requirements['global'] = $_POST['global'];
        $requirements['segments'] = [];
        /**
         * Ugly filtering of post data in order to map form data correctly to diagram APIs
         */
        foreach ($_POST['segments'] as $keyO => $data) {
            //var_dump($data['data_requirements']['technical_metrics']);
            $requirements['segments'][$keyO] = $data;

            if (is_string($data['data_requirements']['technical_metrics']) && strpos($data['data_requirements']['technical_metrics'], '|') !== false) {
                $e = explode('|', $data['data_requirements']['technical_metrics']);
                $requirements['segments'][$keyO]['data_requirements']['technical_metrics'] = [$e[0] => $e[1]];

                continue;
            }
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

        $diagramOrchestrator = new DiagramOrchestrator(
            $requirements,
            $this->getDoctrine()
        );

        $res = $diagramOrchestrator->process();

        foreach ($res[1] as $key => $day) {
            if(empty($day)) {
                unset($res[1][$key]);
            }
        }

        $reversedDays = array_reverse($res[1]);

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
