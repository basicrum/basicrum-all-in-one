<?php

declare(strict_types=1);

namespace App\Controller;

use App\BasicRum\Beacon\RumData\ResourceTiming;
use App\BasicRum\ResourceSize;
use App\BasicRum\WaterfallSvgRenderer;
use App\Entity\RumDataFlat;
use App\Entity\RumDataUserAgents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BeaconController extends AbstractController
{
    /**
     * @Route("/diagrams/beacon/draw", name="diagrams_beacon_draw")
     */
    public function draw()
    {
        /**
         * TODO: refactor page_view_id in front-end.
         */
        $rumDataId = (int) $_POST['page_view_id'];

        /** @var RumDataFlat $rumDataFlat */
        $rumDataFlat = $this->getDoctrine()
            ->getRepository(RumDataFlat::class)
            ->findBy(['rumDataId' => $rumDataId]);

        /** @var RumDataUserAgents $userAgent */
        $userAgent = $this->getDoctrine()
            ->getRepository(RumDataUserAgents::class)
            ->findBy(['id' => $rumDataFlat[0]->getUserAgentId()]);

        $sizeDistribution = [];

        $resourceTiming = new ResourceTiming();

        $resourceTimingsData = $resourceTiming->fetchResources($rumDataId, $this->getDoctrine());

        if (!empty($resourceTimingsData)) {
            $resourceSizesCalculator = new ResourceSize();
            $sizeDistribution = $resourceSizesCalculator->calculateSizes($resourceTimingsData);
        }

        $timings = [
            'nt_nav_st' => 0,
            'nt_first_paint' => $rumDataFlat[0]->getFirstContentfulPaint(),
            'nt_res_st' => $rumDataFlat[0]->getFirstByte(),
            'restiming' => $resourceTimingsData,
        ];

        $renderer = new WaterfallSvgRenderer();

        return $this->json([
            'waterfall' => $renderer->render($timings),
            'resource_distribution' => [
                'labels' => array_keys($sizeDistribution),
                'values' => array_values($sizeDistribution),
            ],
            'user_agent' => $userAgent[0]->getUserAgent(),
            'browser_name' => $userAgent[0]->getBrowserName(),
        ]);
    }
}
