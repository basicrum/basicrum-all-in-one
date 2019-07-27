<?php

declare(strict_types=1);

namespace App\BasicRum\Date;

use App\BasicRum\Date\TimePeriod\Interval;

use \DateTime;

class TimePeriod
{

    const TAIL_TIME = ' 00:00:00';

    /**
     * @param int $days
     * @return Interval
     * @throws \Exception
     */
    public function getPastDaysFromNow(int $days)
    {
        $today = new DateTime();
        $start = $today->modify("-{$days} days");

        return $this->_createInterval($start);
    }

    /**
     * @param int $weeks
     * @return Interval
     * @throws \Exception
     */
    public function getPastWeeksFromNow(int $weeks)
    {
        $today = new DateTime();
        $start = $today->modify("-{$weeks} weeks");

        return $this->_createInterval($start);
    }

    /**
     * @param int $months
     * @return Interval
     * @throws \Exception
     */
    public function getPastMonthsFromNow(int $months)
    {
        $today = new DateTime();
        $start = $today->modify("-{$months} months");

        return $this->_createInterval($start);
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function _getEndOfNowPeriod()
    {
        $today = new DateTime();
        $tomorrow = $today->modify("+1 day");

        return $this->_prepareIntervalValue($tomorrow);
    }

    /**
     * @param DateTime $date
     * @return string
     */
    private function _prepareIntervalValue(DateTime $date)
    {
        return $date->format('m/d/Y');
    }

    /**
     * @param DateTime $start
     * @return Interval
     * @throws \Exception
     */
    private function _createInterval(DateTime $start)
    {
        return new Interval($this->_prepareIntervalValue($start), $this->_getEndOfNowPeriod());
    }

}