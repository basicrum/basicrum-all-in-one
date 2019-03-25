<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\BasicRum\WaterfallSvgRenderer;

use App\BasicRum\ResourceTiming\Decompressor;
use App\BasicRum\ResourceSize;

use App\Entity\NavigationTimings;
use App\Entity\ResourceTimings;
use App\Entity\ResourceTimingsUrls;
use App\Entity\NavigationTimingsUserAgents;


class BeaconController extends AbstractController
{

    /**
     * @Route("/diagrams/beacon/draw", name="diagrams_beacon_draw")
     */
    public function draw()
    {
        $pageViewId = $_POST['page_view_id'];

        /** @var NavigationTimings $navigationTiming */
        $navigationTiming = $this->getDoctrine()
            ->getRepository(NavigationTimings::class)
            ->findBy(['pageViewId' => $pageViewId]);


        /** @var ResourceTimings $resourceTimings */
        $resourceTimings = $this->getDoctrine()
            ->getRepository(ResourceTimings::class)
            ->findBy(['pageViewId' => $pageViewId]);

        /** @var NavigationTimingsUserAgents $userAgent */
        $userAgent = $this->getDoctrine()
            ->getRepository(NavigationTimingsUserAgents::class)
            ->findBy(['id' => $navigationTiming[0]->getUserAgentId()]);

        $decompressor = new Decompressor();

        $resourceTimingsDecompressed = [];

        /** @var ResourceTimings $res */
        foreach ($resourceTimings as $res) {
            $resourceTimingsDecompressed = $decompressor->decompress($res->getResourceTimings());
        }

        $resourceTimingsData = [];

        foreach ($resourceTimingsDecompressed as $res) {
            // We do this in guly way but so far nice looking code is not a priority
            /** @var \App\Entity\ResourceTimingsUrls $resourceTimingUrl */
            $resourceTimingUrl = $this->getDoctrine()
                ->getRepository(ResourceTimingsUrls::class)
                ->findOneBy(['id' => $res['url_id']]);

            $resourceTimingsData[] = [
                'name'                  => $resourceTimingUrl->getUrl(),
                'initiatorType'         => 1,
                'startTime'             => $res['start'],
                'redirectStart'         => 0,
                'redirectEnd'           => 0,
                'fetchStart'            => 0,
                'domainLookupStart'     => 0,
                'domainLookupEnd'       => 0,
                'connectStart'          => 0,
                'secureConnectionStart' => 0,
                'connectEnd'            => 0,
                'requestStart'          => 0,
                'responseStart'         => 0,
                'responseEnd'           => $res['start'] + $res['duration'],
                'duration'              => $res['duration'],
                'encodedBodySize'       => 5,
                'transferSize'          => 11,
                'decodedBodySize'       => 52
            ];
        }


        $sizeDistribution = [];

        if (!empty($resourceTimingsData)) {
            $resourceSizesCalculator = new ResourceSize();
            $sizeDistribution = $resourceSizesCalculator->calculateSizes($resourceTimingsData);
        }

        $timings = [
            'nt_nav_st'      => 0,
            'nt_first_paint' => $navigationTiming[0]->getFirstContentfulPaint(),
            'nt_res_st'      => $navigationTiming[0]->getFirstByte(),
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
                    'user_agent'            => $userAgent[0]->getUserAgent(),
                    'browser_name'          => $userAgent[0]->getBrowserName()
                ]
            )
        );

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}
