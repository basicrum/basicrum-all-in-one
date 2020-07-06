<?php

namespace App\Controller;

use App\Entity\RumDataFlat;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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

        $bumpNowDate = '';

        if ('' !== $globalNotification) {
            /** @var NavigationTimings $lastNavigationTiming */
            $lastNavigationTiming = $this->getDoctrine()
                ->getManager()
                ->createQueryBuilder()
                ->select('e')
                ->from(RumDataFlat::class, 'e')
                ->orderBy('e.rumDataId', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if (null === $lastNavigationTiming) {
                $lastNavigationTiming = new \DateTime();
                $endTestData = $lastNavigationTiming->format('F j, Y');
            } else {
                $endTestData = $lastNavigationTiming->getCreatedAt()->format('F j, Y');
            }

            $bumpNowDate = $endTestData;
            $bumpNowDate .= ' 00:00:00';

            /** @var NavigationTimings $lastNavigationTiming */
            $firstNavigationTiming = $this->getDoctrine()
                ->getManager()
                ->createQueryBuilder()
                ->select('e')
                ->from(RumDataFlat::class, 'e')
                ->orderBy('e.rumDataId', 'ASC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if (null === $firstNavigationTiming) {
                $firstNavigationTiming = new \DateTime();
                $startTestData = $firstNavigationTiming->format('F j, Y');
            } else {
                $startTestData = $firstNavigationTiming->getCreatedAt()->format('F j, Y');
            }
        }

        return $this->render(
            'index.html.twig',
            [
                'global_notification' => $globalNotification,
                'start_test_data' => $startTestData,
                'end_test_data' => $endTestData,
                'bump_now_date' => $bumpNowDate,
            ]
        );
    }
}
