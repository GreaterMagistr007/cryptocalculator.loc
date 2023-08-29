<?php

namespace App\Models;

use Carbon\Carbon;

class MonthCalculator
{
    public Carbon $startDate;
    public Carbon $endDate;

    public int $startTimestamp;
    public int $endTimestamp;
    public string $startDateTime;
    public string $endDateTime;

    public array $weeks = [];

    const DATETIME_FORMAT = 'd.m.Y H:i:s';

    const MONTH_TITLES_TRANSLATIONS = [
        'January' => 'Январь',
        'February' => 'Февраль',
        'March' => 'Март',
        'April' => 'Апрель',
        'May' => 'Май',
        'June' => 'Июнь',
        'July' => 'Июль',
        'August' => 'Август',
        'September' => 'Сентябрь',
        'October' => 'Октябрь',
        'November' => 'Ноябрь',
        'December' => 'Декабрь',
    ];

    public function __construct(Carbon $dateOfMonth)
    {
        $date = Carbon::createFromTimestamp($dateOfMonth->getTimestamp());
        $this->startDate = Carbon::createFromTimestamp($date->startOfMonth()->getTimestamp());
        $this->endDate = Carbon::createFromTimestamp($date->endOfMonth()->getTimestamp());

        $this->startTimestamp = $this->startDate->getTimestamp();
        $this->endTimestamp = $this->endDate->getTimestamp();

        $this->startDateTime = $this->startDate->format(self::DATETIME_FORMAT);
        $this->endDateTime = $this->endDate->format(self::DATETIME_FORMAT);

        $this->divideByWeeks();
    }

    /**
     * Русское название месяца
     * @return string
     */
    public function getMonthTitle():string
    {
        return isset(self::MONTH_TITLES_TRANSLATIONS[$this->startDate->format('F')]) ?
            self::MONTH_TITLES_TRANSLATIONS[$this->startDate->format('F')] :
            $this->startDate->format('F');
    }

    /**
     * Предыдущий месяц
     * @return MonthCalculator
     */
    public function getPreviousMonth()
    {
        return new self(Carbon::createFromTimestamp($this->startTimestamp)->subDay());
    }

    public function getNumber()
    {
        return $this->startDate->format('m');
    }

    public function divideByWeeks()
    {
        $date = Carbon::createFromTimestamp($this->startDate->getTimestamp());

        $i = 0;
        while ($date <= $this->endDate) {
            $i++;
            $week = new WeekCalculator($date);
            $this->weeks[] = $week;

            $date = $week->getNextWeekInMonth()->startDate;

            if ($i > 10) {
                break;
            }
        }
    }
}
