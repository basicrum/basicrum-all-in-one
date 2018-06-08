<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DiagramsController extends Controller
{

    /**
     * @Route("/diagrams/release/compare", name="diagrams_release_compare")
     */
    public function releaseCompare()
    {
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
            ]
        );
    }

    /**
     * @Route("/diagrams/waterfalls/list", name="diagrams_waterfalls_list")
     */
    public function waterfallsList()
    {
        return $this->render('diagrams/waterfalls_list.html.twig');
    }

}
