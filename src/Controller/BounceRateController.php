<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use DateTime;
use DatePeriod;
use DateInterval;

use App\BasicRum\BounceRate;

class BounceRateController extends AbstractController
{


    /**
     * @Route("/diagrams/bounce_rate/distribution", name="diagrams_bounce_rate_distribution")
     */
    public function firstPaintDistribution()
    {
        // Quick hack for out of memory problems
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $sessionsCount = 0;
        $bouncesCount = 0;
        $convertedSessions = 0;

        $dateConditionStart = '2018-12-10';
        $dateConditionEnd   = '2019-01-24';

//        $dateConditionStart = '2018-10-24';
//        $dateConditionEnd   = '2018-12-10';

        $dateConditionStart = '2018-10-24';
        $dateConditionEnd   = '2018-10-25';

        // Test periods
        $periodChunks = $this->_gerPeriodDays($dateConditionStart, $dateConditionEnd);

        $groupMultiplier = 200;
        $upperLimit = 5000;

        $firstPaintArr = [];
        $allFirstPaintArr = [];
        $bouncesGroup  = [];
        $bouncesPercents = [];

        // Init the groups/buckets
        for($i = $groupMultiplier; $i <= $upperLimit; $i += $groupMultiplier) {
            $allFirstPaintArr[$i] = 0;
        }

        for($i = $groupMultiplier; $i <= $upperLimit; $i += $groupMultiplier) {
            $firstPaintArr[$i] = 0;
            $allFirstPaintArr[$i] = 0;
            if ($i >= 150 && $i <= $upperLimit) {
                $bouncesGroup[$i] = 0;
            }
        }

        $bounceRateReport = new BounceRate($this->getDoctrine());

        foreach ($periodChunks as $day) {
            $dayReport = $bounceRateReport->getInMetricInPeriod($day['start'], $day['end']);

            $convertedSessions += count($dayReport['converted_sessions']);

            foreach ($dayReport['all_sessions'] as $guid => $ttfp) {

                $paintGroup = $groupMultiplier * (int) ($ttfp / $groupMultiplier);

                if ($upperLimit >= $paintGroup && $paintGroup > 0) {
                    $allFirstPaintArr[$paintGroup]++;
                }

                if ($upperLimit >= $paintGroup && $paintGroup > 0) {

                    if ($paintGroup >= 150 && $paintGroup  <= $upperLimit) {
                        $firstPaintArr[$paintGroup]++;
                        $sessionsCount++;

                        if (isset($dayReport['bounced_sessions'][$guid])) {
                            $bouncesCount++;

                            $bouncesGroup[$paintGroup]++;

                        }
                    }
                }
            }
        }

        $xAxisLabels = [];

        foreach($firstPaintArr as $paintGroup => $numberOfProbes) {
            $time = ($paintGroup / 1000);

            $xAxisLabels[] = $time;

            if ($numberOfProbes > 0) {
                if ($paintGroup >= 150 && $paintGroup <= $upperLimit) {
                    $bouncesPercents[$paintGroup] = (int) number_format(($bouncesGroup[$paintGroup] / $numberOfProbes) * 100);
                }
            }
        }

        return $this->render('diagrams/bounce_first_paint.html.twig',
            [
                'count'             => $sessionsCount,
                'bounceRate'        => (int) number_format(($bouncesCount / $sessionsCount) * 100),
                'conversionRate'    => (int) number_format(($convertedSessions / $sessionsCount) * 100),
                'x1Values'          => json_encode(array_keys($firstPaintArr)),
                'y1Values'          => json_encode(array_values($firstPaintArr)),
                'x2Values'          => json_encode(array_keys($bouncesPercents)),
                'y2Values'          => json_encode(array_values($bouncesPercents)),
                'annotations'       => json_encode($bouncesPercents),
                'x_axis_labels'     => json_encode(array_values($xAxisLabels)),
                'startDate'         => $dateConditionStart,
                'endDate'           => $dateConditionEnd
            ]
        );
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     */
    private function _gerPeriodDays($startDate, $endDate)
    {
        $calendarDayFrom = $startDate;
        $calendarDayTo = $endDate;

        $period = new DatePeriod(
            new DateTime($calendarDayFrom),
            new DateInterval('P1D'),
            new DateTime($calendarDayTo)
        );

        $betweenArr = [];

        foreach ($period as $key => $value) {
            $calendarDay = $value->format('Y-m-d');

            $betweenArr[] = [
                'start' => $calendarDay . ' 00:00:01',
                'end'   => $calendarDay  . ' 23:59:59'
            ];
        }

        return $betweenArr;
    }

}
