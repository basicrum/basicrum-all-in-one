<?php
/**
 * Proof of concept logic that fixes time for first paint and writes speculative
 * time based on the last loaded render critical resource
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\Entity\NavigationTimings;
use App\Entity\ResourceTimings;

class DataFixController extends Controller
{

    /**
     * @Route("/data_fix/speculative_first_paint", name="data_fix_speculative_first_paint")
     */
    public function index()
    {
        $repository = $this->getDoctrine()
            ->getRepository(NavigationTimings::class);

        $query = $repository->createQueryBuilder('nt')
            ->where("nt.url LIKE '%GOO-%' AND nt.userAgent NOT LIKE '%Googlebot%' AND nt.ptFp = 0 AND nt.speculativeFp IS NULL")
            ->orderBy('nt.pageViewId', 'DESC')
            ->setMaxResults(100)
            ->getQuery();

        $navigationTimings = $query->getResult();

        foreach ($navigationTimings as $nav)
        {
            /** @var array $resourceTimings */
            $resourceTimings = $this->getDoctrine()
                ->getRepository(ResourceTimings::class)
                ->findBy(['pageView' => $nav->getPageViewId()], ['starttime' => 'ASC']);

            $highest = 0;

            foreach ($resourceTimings as $res) {
                $url = $res->getUrl();
                //echo $url;
                // We can add optmizely later
                if (strpos($url, 'www.hundeland.de') > 0 && (strpos($url, '.js') > 0 || strpos($url, '.css') > 0)) {
                    $busyTime = $res->getStartTime() + $res->getDuration();

                    if ($busyTime > $highest) {
                        $highest = $busyTime;
                    }
                }
            }

            //echo $highest . ' : ' . $nav->getUrl();

            $navUpdate = $this->getDoctrine()
                ->getRepository(NavigationTimings::class)
                ->find($nav->getPageViewId());

            $navUpdate->setSpeculativeFp($highest);

            $entityManager = $this->getDoctrine()->getManager();

            // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $entityManager->persist($navUpdate);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();
        }

        return $this->render('data/repeat_data_fix.html.twig',
            [
                'count'   => count($navigationTimings)
            ]
        );
    }

}
