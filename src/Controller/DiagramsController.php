<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\BasicRum\WaterfallSvgRenderer;
use App\BasicRum\ResourceTimingDecompressor_v_0_3_4;
use App\BasicRum\ResourceSize;

use App\Entity\PageTypeConfig;
use App\Entity\NavigationTimings;
use App\Entity\ResourceTimings;


class DiagramsController extends Controller
{

    /**
     * @Route("/diagrams/builder", name="diagrams_builder")
     */
    public function diagramsBuilder()
    {
        return $this->render('diagrams/diagram_builder.html.twig');
    }

    /**
     * @Route("/diagrams/release/compare", name="diagrams_release_compare")
     */
    public function releaseCompare()
    {
        $pageTypes = $this->getDoctrine()
            ->getRepository(PageTypeConfig::class)
            ->findAll();


        $prevPeriod = '{"0":"0.00","200":"0.33","400":"0.66","600":"1.16","800":"1.66","1000":"2.11","1200":"2.87","1400":"3.79","1600":"4.02","1800":"4.63","2000":"5.06","2200":"5.47","2400":"5.81","2600":"6.09","2800":"5.93","3000":"5.67","3200":"5.33","3400":"4.95","3600":"4.46","3800":"3.61","4000":"3.44","4200":"3.10","4400":"2.59","4600":"2.28","4800":"1.98","5000":"1.74","5200":"1.64","5400":"1.45","5600":"1.15","5800":"1.11","6000":"0.98","6200":"0.80","6400":"0.63","6600":"0.66","6800":"0.61","7000":"0.61","7200":"0.43","7400":"0.34","7600":"0.42","7800":"0.43","8000":"0.00"}';
        $nextPeriod = '{"0":"0.00","200":"0.37","400":"0.69","600":"1.40","800":"1.77","1000":"2.78","1200":"3.41","1400":"3.84","1600":"4.40","1800":"4.99","2000":"5.24","2200":"5.75","2400":"6.11","2600":"5.92","2800":"5.98","3000":"5.43","3200":"5.19","3400":"4.47","3600":"4.00","3800":"3.59","4000":"3.15","4200":"2.88","4400":"2.34","4600":"2.12","4800":"1.95","5000":"1.74","5200":"1.49","5400":"1.28","5600":"1.03","5800":"1.05","6000":"0.85","6200":"0.83","6400":"0.67","6600":"0.62","6800":"0.65","7000":"0.58","7200":"0.45","7400":"0.37","7600":"0.33","7800":"0.30","8000":"0.00"}';

        $prevPeriod = json_decode($prevPeriod, true);
        $nextPeriod = json_decode($nextPeriod, true);

        $prevPeriodKeys   = json_encode(array_keys($prevPeriod));
        $prevPeriodValues = json_encode(array_values($prevPeriod));

        $nextPeriodKeys   = json_encode(array_keys($nextPeriod));
        $nextPeriodValues = json_encode(array_values($nextPeriod));

        return $this->render('diagrams/release_compare.html.twig',
            [
                'prev_period_keys'   => $prevPeriodKeys,
                'prev_period_values' => $prevPeriodValues,
                'next_period_keys'   => $nextPeriodKeys,
                'next_period_values' => $nextPeriodValues,
                'page_types'         => $pageTypes
            ]
        );
    }

    /**
     * @Route("/diagrams/first_paint/distribution", name="diagrams_first_paint_distribution")
     */
    public function firstPaintDistribution()
    {
        // Quick hack for out of memory problems
        ini_set('memory_limit', -1);

        $repository = $this->getDoctrine()
            ->getRepository(NavigationTimings::class);

        $conditionString = 'psm=GOO-0816-09';
        $conditionString = 'psm=GOO-';

        $dateCond = $dateConditionStart = '2018-08-24';

        $query = $repository->createQueryBuilder('nt')
            ->where("nt.url LIKE '%" . $conditionString . "%' AND nt.userAgent NOT LIKE '%1Googlebot%' AND ((nt.ptFp > 0 AND nt.ptFp < 10000) OR (nt.speculativeFp > 0 AND nt.speculativeFp < 10000)) AND nt.createdAt >= '" . $dateCond . "'")
            ->orderBy('nt.createdAt', 'DESC')
            ->getQuery();

        $navigationTimings = $query->getResult();

        $groupMultiplier = 200;
        $upperLimit = 10000;
        $firstPaintArr = [];
        $bouncesGroup  = [];
        $bouncesPercents = [];

        $sessions = [];

        $count = 0;
        $bounces = 0;

        for($i = $groupMultiplier; $i <= $upperLimit; $i += $groupMultiplier) {
            $firstPaintArr[$i] = 0;
            $bouncesGroup[$i] = 0;
        }

        foreach ($navigationTimings as $nav)
        {
            $guid = $nav->getGuid();

            $val = $nav->getPtFp();
            if ($val <= 0 && $nav->getSpeculativeFp() > 0) {
                $val = $nav->getSpeculativeFp() + 250;
            }

            $paintGroup = $groupMultiplier * (int) ($val / $groupMultiplier);

            if ($upperLimit >= $paintGroup && $paintGroup > 0) {
//                $firstPaintArr[$paintGroup]++;
                $sessions[$guid] = $paintGroup;
            }
        }

        foreach ($sessions as $guid => $paintGroup) {
            $repository = $this->getDoctrine()
                ->getRepository(NavigationTimings::class);

            $query = $repository->createQueryBuilder('nt')
                ->where("nt.guid = :guid AND nt.createdAt >= '" . $dateCond . "'")
                ->setParameter('guid', $guid)
                ->orderBy('nt.createdAt', 'ASC')
                ->getQuery();

            $navigationTimings = $query->getResult();

            // Test
//            echo $guid;
//            echo '<br />';

            $timeStamps = [];


//            foreach ($navigationTimings as $timing) {
//                echo $timing->getUrl();
//                echo '<br />';
//                $timeStamps[] = $timing->getCreatedAt()->getTimestamp();
//                echo '-----';
//                echo '<br />';
//            }
//
//            foreach ($timeStamps as $key => $timestamp) {
//                if ($key > 0) {
//                    $diffTime = $timestamp - $timeStamps[$key - 1];
//                    echo $diffTime;
//                    if ($diffTime > 800) {
//                        echo ' Bounce Session';
//                    }
//                    echo '<br />';
//                }
//            }

            // Start:
            // ==============================================================================
            // Filter just bounced sessions and sessions that just interacted with the website but
            // Didn't abuse google shopping and came back over google again and again
            //echo $navigationTimings[0]->getUrl();

//            echo $guid;
//            echo '<br />';
//            echo count($navigationTimings);
//            echo '<br />';
//            echo '<br />';

            if (strpos($navigationTimings[0]->getUrl(), $conditionString) === false) {
                //bounce logic / do not count
                continue;
            }
            // End:
            // ==============================================================================


            // Start: Check if the person has first view but also came back later from google
            // ==============================================================================
            $shouldSkip = true;
            if (count($navigationTimings) >= 2 && ($navigationTimings[0]->getUrl() == $navigationTimings[1]->getUrl())) {
                foreach ($navigationTimings as $key => $timing) {
                    if ($key <= 2) {
                        continue;
                    }

                    if ((strpos($timing->getUrl(), $conditionString) !== false)) {
                        $shouldSkip = false;
                        break;
                    }
                }
            }
            if ($shouldSkip === false) {
                continue;
            }
            // End: Check if the person has first view but also came back later from google
            // ==============================================================================

            if (count($navigationTimings) <= 2) {
//                echo 'Bounce!!!';
//                echo '<br />';

                $bouncesGroup[$paintGroup]++;
                $bounces++;
            }

//            echo '========================================================================================';
//            echo '<br />';

            $count++;
            $firstPaintArr[$paintGroup]++;
        }

        foreach($firstPaintArr as $paintGroup => $numberOfProbes) {
            if ($numberOfProbes > 0) {
                $bouncesPercents[$paintGroup] = (int) number_format(($bouncesGroup[$paintGroup] / $numberOfProbes) * 100);
            }
        }

        return $this->render('diagrams/diagram_first_paint.html.twig',
            [
                'count'       => $count,
                'bounceRate'  => (int) number_format(($bounces / $count) * 100),
                'x1Values'    => json_encode(array_keys($firstPaintArr)),
                'y1Values'    => json_encode(array_values($firstPaintArr)),
                'x2Values'    => json_encode(array_keys($bouncesPercents)),
                'y2Values'    => json_encode(array_values($bouncesPercents)),
                'annotations' => json_encode($bouncesPercents)
            ]
        );
    }

    /**
     * @Route("/diagrams/waterfalls/list", name="diagrams_waterfalls_list")
     */
    public function waterfallsList()
    {
        $repository = $this->getDoctrine()
            ->getRepository(NavigationTimings::class);
        // createQueryBuilder() automatically selects FROM AppBundle:Product
        // and aliases it to "p"
        $query = $repository->createQueryBuilder('nt')
            ->where("nt.url LIKE '%GOO%'")
            //->setParameter('url', 'GOO')
            ->orderBy('nt.pageViewId', 'DESC')
            ->setMaxResults(400)
            ->getQuery();

        $navigationTimings = $query->getResult();

        $beacon = '{"https://www.":{"darvart.de/":{"holzfliegen.html":"6,29s,14c,ia,i9,dt,5q,5q,6*1obr,gj,2kz0","skin/frontend/darvart/default/images/":{"darvart-navy-logo.png":"*01y,1y,1l,2t,2s,2s|129s,2h6,2h5,4*12h5,bs","icon_sprite@2x.png":"42a5,35t,346,2gr,2gr,2,2*18m7,bv","opc-ajax-loader.gif":"42at,358,357,2g5*15sj,bt"},"media/":{"js/387cfdcd3b7e1ac11df96a1adb39c25b.js":"32a1,4ap,2gw,1*12yh1,h2,8pfd*24","catalog/product/cache/1/small_image/280x/17f82f742ffe127f42dca9de82fb58b1/d/a/darvena-papi":{"jonka-dilov-01.jpg":"*09l,7q,86,2o,9o,7s|12b4,3nr,3mk,34v*18j0,bv","onka-":{"mini-rudolf.jpg":"*09l,7q,86,b7,9o,7s|12b5,3nq,3n6,34x*161j,bu","classic-rudolf_1.jpg":"*09l,7q,86,jq,9o,7s|12b5,3yb,3y8,3j2,3j0,2ye,2ft,2ft,2ft*16ky,bv","golyam-sechko.jpg":"*09l,7q,86,s9,9o,7s|12b5,3zo,3y9,3j2,3j1,34t,34t,34t,34t*19ef,bw"}}}},"google-analytics.com/":{"analytics.js":"36md,2b,22,5*1b3m,5g,g40*25","collect?v=1&_v=j68&aip=1&a=1185933321&t=pageview&_s=1&dl=https%3A%2F%2Fwww.darvart.de%2Fholzfliegen.html&ul=en-us&de=UTF-8&dt=Handgefertigte%20Holzfliegen%20f%C3%BCr%20M%C3%A4nner%20und%20Frauen%20%7C%20DarvArt.de&sd=24-bit&sr=1440x900&vp=1391x304&je=0&_u=QACAAEAB~&jid=&gjid=&cid=1241074160.1507998957&tid=UA-89019502-1&_gid=1486960115.1528871993&z=49870228":"16p8,1s"}}}';

        $renderer = new WaterfallSvgRenderer();
        $resTimingDecompressor = new ResourceTimingDecompressor_v_0_3_4();

        $res = $resTimingDecompressor->decompressResources(json_decode($beacon, true));
        $resourceSizesCalculator = new ResourceSize();

        $timings = [
            'nt_nav_st'      => 0,
            'nt_first_paint' => 2480,
            'nt_res_st'      => 1800,
            'restiming'      => $res,
            'url'            => 'https://www.darvart.de/'
        ];

        $resourceSizes = $resourceSizesCalculator->calculateSizes($res);

        $navTimingsFiltered = [];

        foreach ($navigationTimings as $navTiming) {
            if ($navTiming->getPtFcp() > 0) {
                $navTimingsFiltered[] = $navTiming;
            }
        }

        return $this->render(
            'diagrams/waterfalls_list.html.twig',
            [
                'page_views'            => $navTimingsFiltered,
                'waterfallHtml'         => $renderer->render($timings),
                'resource_sizes_labels' => json_encode(array_keys($resourceSizes)),
                'resource_sizes_values' => json_encode(array_values($resourceSizes))
            ]
        );
    }

    /**
     * @Route("/diagrams/beacon/draw", name="diagrams_beacon_draw")
     */
    public function beaconDraw()
    {
        $pageViewId = $_POST['page_view_id'];

        /**
         * Start getting page view
         */
        /** @var NavigationTimings $navigationTiming */
        $navigationTiming = $this->getDoctrine()
            ->getRepository(NavigationTimings::class)
            ->findBy(['pageViewId' => $pageViewId]);
        /**
         * End getting page view
         */

        /** @var array $resourceTimings */
        $resourceTimings = $this->getDoctrine()
            ->getRepository(ResourceTimings::class)
            ->findBy(['pageView' => $pageViewId], ['starttime' => 'ASC']);


        $resourceTimingsData = [];

        /** @var ResourceTimings $res */
        foreach ($resourceTimings as $res) {
            $resourceTimingsData[] = [
                'name'                  => $res->getUrl(),
                'initiatorType'         => $res->getInitiatortype(),
                'startTime'             => $res->getStarttime(),
                'redirectStart'         => $res->getRedirectStart(),
                'redirectEnd'           => $res->getRedirectEnd(),
                'fetchStart'            => $res->getFetchStart(),
                'domainLookupStart'     => $res->getDomainLookupEnd(),
                'domainLookupEnd'       => $res->getDomainLookupEnd(),
                'connectStart'          => $res->getConnectStart(),
                'secureConnectionStart' => $res->getSecureConnectionStart(),
                'connectEnd'            => $res->getConnectEnd(),
                'requestStart'          => $res->getRequestStart(),
                'responseStart'         => $res->getResponseStart(),
                'responseEnd'           => $res->getResponseEnd(),
                'duration'              => $res->getDuration(),
                'encodedBodySize'       => $res->getEncodedBodySize(),
                'transferSize'          => $res->getTransferSize(),
                'decodedBodySize'       => $res->getDecodedBodySize()
            ];
        }


        $sizeDistribution = [];

        if (!empty($resourceTimingsData)) {
            $resourceSizesCalculator = new ResourceSize();
            $sizeDistribution = $resourceSizesCalculator->calculateSizes($resourceTimingsData);
        }

        $timings = [
            'nt_nav_st'      => 0,
            'nt_first_paint' => $navigationTiming[0]->getPtFcp(),
            'nt_res_st'      => $navigationTiming[0]->getNtResSt() - $navigationTiming[0]->getNtNavSt(),
            'restiming'      => $resourceTimingsData,
            'url'            => 'https://www.darvart.de/'
        ];

        $renderer = new WaterfallSvgRenderer();

        $response = new Response(
            json_encode(
                [
                    'waterfall'             => $renderer->render($timings),
                    'resource_distribution' =>
                        [
                            'labels' => array_keys($sizeDistribution),
                            'values' => array_values($sizeDistribution)
                        ],
                    'user_agent'            => $navigationTiming[0]->getUserAgent()
                ]
            )
        );

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}
