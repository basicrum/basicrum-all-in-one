<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ResourceTimings;
use App\Entity\RumDataFlat;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JourneyController extends AbstractController
{
    /**
     * @Route("/journey/list", name="journey_list")
     */
    public function journeyList()
    {
        $start = '2018-10-24 00:00:01';
        $end = '2018-10-24 23:59:59';

//        $start = '2018-09-19 00:00:01';
//        $end   = '2018-09-19 23:59:59';

        $repository = $this->getDoctrine()
            ->getRepository(RumDataFlat::class);
        // createQueryBuilder() automatically selects FROM AppBundle:Product
        // and aliases it to "p"
        $query = $repository->createQueryBuilder('nt')
            ->select('nt.rt_si', 'nt.rumDataId')
            ->where("nt.createdAt BETWEEN '".$start."' AND '".$end."'")
            //->setParameter('url', 'GOO')
            ->orderBy('nt.rumDataId', 'DESC')
            ->setMaxResults(100)
            ->groupBy('nt.rumDataId, nt.rt_si')
            ->getQuery();

        $navigationTimings = $query->getResult();
//
//        print_r($navigationTimings);

        return $this->render('diagrams/journey_list.html.twig',
            [
                'page_views' => $navigationTimings,
            ]
        );
    }

    /**
     * @Route("/journey/draw", name="journey_draw")
     */
    public function journeyDraw()
    {
        $rt_si = $_POST['rt_si'];

        /** @var NavigationTimings $navigationTiming */
        $navigationTimings = $this->getDoctrine()
            ->getRepository(RumDataFlat::class)
            ->findBy(['rt_si' => $rt_si]);

        $filteredNavigations = [];

        foreach ($navigationTimings as $nav) {
            /** @var NavigationTimings $navigationTiming */
            $resourceTimings = $this->getDoctrine()
                ->getRepository(ResourceTimings::class)
                ->findBy(['pageView' => $nav->getRumDataId()]);

            if (\count($resourceTimings) > 0) {
                $filteredNavigations[] = $nav;
            }
        }

        $response = new Response(
            json_encode(
                [
                    'page_views' => $this->get('twig')->render(
                            'diagrams/journey/page_views_table.html.twig', ['page_views' => $filteredNavigations]
                        ),
                ]
            )
        );

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
