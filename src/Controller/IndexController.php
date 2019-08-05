<?php

namespace App\Controller;

use App\Entity\NavigationTimings;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    /**
     * @Route("/index", name="page_index")
     */
    public function index()
    {
        $globalNotification = $_ENV['APP_ENV'] ?? '';
        $globalNotification = $_ENV['APP_DEMO_MODE'] ?? $globalNotification;

        $startTestData = '';
        $endTestData = '';

        if( '' !== $globalNotification ) {
            /** @var NavigationTimings $lastNavigationTiming */
            $lastNavigationTiming = $this->getDoctrine()
                ->getManager()
                ->createQueryBuilder()
                ->select('e')
                ->from(NavigationTimings::class, 'e')
                ->orderBy('e.pageViewId', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            $endTestData = $lastNavigationTiming->getCreatedAt()->format('F j, Y');

            /** @var NavigationTimings $lastNavigationTiming */
            $firstNavigationTiming = $this->getDoctrine()
                ->getManager()
                ->createQueryBuilder()
                ->select('e')
                ->from(NavigationTimings::class, 'e')
                ->orderBy('e.pageViewId', 'ASC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            $startTestData = $firstNavigationTiming->getCreatedAt()->format('F j, Y');
        }

        return $this->render(
            'index.html.twig',
            [
                'global_notification' => $globalNotification,
                'start_test_data'     => $startTestData,
                'end_test_data'       => $endTestData
            ]
        );
    }

}
