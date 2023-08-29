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

    public float $average;

    public array $weeks = [];

    const DATETIME_FORMAT = 'd.m.Y H:i:s';

    const AVAILABLE_CALCULATE_METHODS = [
        'monthCalculationMethod1' => '(недельное среднее 1 (НС1) + (НС2) + (НС3) + (НС4) + (НС5)) / (количество недель)',
        'monthCalculationMethod2' => '(сумма всех значений за период) / (количество значений)',
        'monthCalculationMethod3' => '(Минимальное + максимальное значение за период) / 2',
    ];

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

        while ($date <= $this->endDate) {
            $week = new WeekCalculator($date);
            $this->weeks[] = $week;

            $date = $week->getNextWeekInMonth()->startDate;
        }
    }

    public function calculateAllWeeks($method)
    {
        foreach ($this->weeks as $week) {
            $week->calculate($method);
        }
    }

    public function calculate($method)
    {
        if (!isset(self::AVAILABLE_CALCULATE_METHODS[$method])) {
            throw new \DomainException('Нет недельного метода расчета "' . $method . '"');
        }

        $this->average = $this->$method();

        return $this->average;
    }

    private function monthCalculationMethod1()
    {
        $sum = 0;
        foreach ($this->weeks as $week) {
            $sum += $week->average;
        }

        return $sum / count($this->weeks);
    }

    private function monthCalculationMethod2()
    {
        $values = Bitcoin::getPricesFromDB($this->startDate, $this->endDate);
        $sum = 0;

        foreach ($values as $value) {
            $sum += $value->price;
        }

        return $sum / count($values);
    }

    private function monthCalculationMethod3()
    {
        $values = Bitcoin::getPricesFromDB($this->startDate, $this->endDate);

        $min = $values[0]->price;
        $max = $values[0]->price;

        foreach ($values as $value) {
            if ($min < $value->price) {
                $min = $value->price;
            }
            if ($max > $value->price) {
                $max = $value->price;
            }
        }

        return ($min + $min) / 2;
    }
}
