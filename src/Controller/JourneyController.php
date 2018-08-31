<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\BasicRum\WaterfallSvgRenderer;
use App\BasicRum\ResourceTimingDecompressor_v_0_3_4;

use App\Entity\NavigationTimings;
use App\Entity\ResourceTimings;


class JourneyController extends Controller
{

    /**
     * @Route("/journey/list", name="journey_list")
     */
    public function journeyList()
    {
        $start = '2018-08-30 00:00:01';
        $end   = '2018-08-30 23:59:59';

        $repository = $this->getDoctrine()
            ->getRepository(NavigationTimings::class);
        // createQueryBuilder() automatically selects FROM AppBundle:Product
        // and aliases it to "p"
        $query = $repository->createQueryBuilder('nt')
            ->where("nt.url LIKE '%psm=GOO-0816-04%' AND nt.createdAt BETWEEN '" . $start . "' AND '" . $end . "'")
            //->setParameter('url', 'GOO')
            ->orderBy('nt.pageViewId', 'DESC')
            //->setMaxResults(100)
            ->groupby('nt.guid')
            ->getQuery();

        $navigationTimings = $query->getResult();

        return $this->render('diagrams/journey_list.html.twig',
            [
                'page_views'   => $navigationTimings
            ]
        );
    }

    /**
     * @Route("/journey/draw", name="journey_draw")
     */
    public function journeyDraw()
    {
        $guid = $_POST['guid'];

        /** @var NavigationTimings $navigationTiming */
        $navigationTimings = $this->getDoctrine()
            ->getRepository(NavigationTimings::class)
            ->findBy(['guid' => $guid]);

        $filteredNavigations = [];

        foreach ($navigationTimings as $nav) {
            /** @var NavigationTimings $navigationTiming */
            $resourceTimings = $this->getDoctrine()
                ->getRepository(ResourceTimings::class)
                ->findBy(['pageView' => $nav->getPageViewId()]);

            if (count($resourceTimings) > 0) {
                $filteredNavigations[] = $nav;
            }
        }


        $response = new Response(
            json_encode(
                [
                    'page_views' => $this->get('twig')->render(
                            'diagrams/journey/page_views_table.html.twig', ['page_views' => $filteredNavigations]
                        )
                ]
            )
        );

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}
