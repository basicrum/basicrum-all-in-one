<?php

namespace App\Tests\BasicRum\Layers\DataLayer\Query;

use App\BasicRum\Filters\Primary\TimeToFirstPaint;
use App\BasicRum\Layers\DataLayer\Query\MainDataSelect\DataRows;
use App\BasicRum\Periods\Period;

//use App\BasicRum\Filters\Primary\TimeToFirstPaint;
//use App\BasicRum\TechnicalMetrics\TimeToFirstPaint;

class FirstPaintSelectTest extends DataLayerFixtureTestCase
{
    /**
     * @group data_query
     */
    public function testFirstPaintEqualsTo()
    {
        $period = new Period();
        $period->setPeriod('10/24/2018', '10/24/2018');

        $firstPaintFilter = new TimeToFirstPaint(
            'is',
            '344'
        );

        $firstPaintSelect = new \App\BasicRum\TechnicalMetrics\TimeToFirstPaint();

        $flavor = new DataRows('rum_data_flat', ['rum_data_id', 'first_paint']);

        $res = $this->getDataLayer()->load(
            $period,
            [$firstPaintFilter, $firstPaintSelect],
            $flavor
        )->process();

        $this->assertEquals(
            [
                '2018-10-24 00:00:00' => [
                    'data_rows' => [
                        [
                            'rum_data_id' => 1,
                            'first_paint' => 344,
                        ],
                    ],
                ],
            ],
            $res
        );
    }
}
