<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\NavigationTimings;
use App\Entity\ResourceTimings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            ->getRepository(NavigationTimings::class);
        // createQueryBuilder() automatically selects FROM AppBundle:Product
        // and aliases it to "p"
        $query = $repository->createQueryBuilder('nt')
            ->select('nt.guid', 'nt.pageViewId')
            ->where("nt.createdAt BETWEEN '".$start."' AND '".$end."'")
            //->setParameter('url', 'GOO')
            ->orderBy('nt.pageViewId', 'DESC')
            ->setMaxResults(100)
            ->groupBy('nt.pageViewId, nt.guid')
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

            if (\count($resourceTimings) > 0) {
                $filteredNavigations[] = $nav;
            }
        }

        return $this->json([
            'page_views' => $this->get('twig')->render(
                'diagrams/journey/page_views_table.html.twig', ['page_views' => $filteredNavigations]
            ),
        ]);
    }
}
