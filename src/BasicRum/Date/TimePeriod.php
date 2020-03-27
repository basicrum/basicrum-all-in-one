<?php

declare(strict_types=1);

namespace App\BasicRum\Date;

use App\BasicRum\Date\TimePeriod\Interval;
use DateTime;

class TimePeriod
{
    /**
     * @return Interval
     *
     * @throws \Exception
     */
    public function getPastDaysFromNow(int $days)
    {
        $today = $this->_getTodayDate();
        $start = $today->modify("-{$days} days");

        return $this->_createInterval($start);
    }

    /**
     * @return Interval
     *
     * @throws \Exception
     */
    public function getPastWeeksFromNow(int $weeks)
    {
        $today = $this->_getTodayDate();
        $start = $today->modify("-{$weeks} weeks");

        return $this->_createInterval($start);
    }

    /**
     * @return Interval
     *
     * @throws \Exception
     */
    public function getPastMonthsFromNow(int $months)
    {
        $today = $this->_getTodayDate();
        $start = $today->modify("-{$months} months");

        return $this->_createInterval($start);
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    private function _getEndOfNowPeriod()
    {
        $today = $this->_getTodayDate();
        $tomorrow = $today->modify('+1 day');

        return $this->_prepareIntervalValue($tomorrow);
    }

    /**
     * @return string
     */
    private function _prepareIntervalValue(DateTime $date)
    {
        return $date->format('m/d/Y');
    }

    /**
     * @return Interval
     *
     * @throws \Exception
     */
    private function _createInterval(DateTime $start)
    {
        return new Interval($this->_prepareIntervalValue($start), $this->_getEndOfNowPeriod());
    }

    /**
     * @return DateTime
     *
     * @throws \Exception
     */
    private function _getTodayDate()
    {
        $now = isset($_POST['BUMP_NOW_DATE']) ? $_POST['BUMP_NOW_DATE'] : '';

        return new DateTime($now);
    }
}
