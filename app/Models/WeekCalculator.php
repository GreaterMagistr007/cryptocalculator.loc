<?php

namespace App\Models;


use Carbon\Carbon;

class WeekCalculator
{
    public Carbon $startDate;
    public Carbon $endDate;

    public int $startTimestamp;
    public int $endTimestamp;
    public string $startDateTime;
    public string $endDateTime;

    public string $calculationMethod; // Текстовое обозначение метода расчета
    public string $calculateMethod; // Название функции метода расчета

    public float $average;

    public Carbon $dateOfMaximum;
    public Carbon $dateOfMinimum;

    const DATETIME_FORMAT = 'd.m.Y H:i:s';

    const AVAILABLE_CALCULATE_METHODS = [
        'weeklyCalculationMethod1' => '(сумма всех значений за период) / (количество значений)',
        'weeklyCalculationMethod2' => '(Минимальное + максимальное значение за период) / 2',
    ];

    public float $minValueOfPeriod;
    public float $maxValueOfPeriod;

    public function __construct(Carbon $startDate)
    {
        $date = Carbon::createFromTimestamp($startDate->getTimestamp())->startOfDay();

        $this->startDate = Carbon::createFromTimestamp($date->getTimestamp());
        $this->endDate = Carbon::createFromTimestamp($date->addDays(6)->endOfDay()->getTimestamp());
        $date = Carbon::createFromTimestamp($this->startDate->getTimestamp());
        $endOfMonth = Carbon::createFromTimestamp($date->lastOfMonth()->endOfDay()->getTimestamp());

        if ($this->endDate > $endOfMonth) {
            $this->endDate = Carbon::createFromTimestamp($endOfMonth->getTimestamp());
        }

        $this->startTimestamp = $this->startDate->getTimestamp();
        $this->endTimestamp = $this->endDate->getTimestamp();

        $this->startDateTime = $this->startDate->format(self::DATETIME_FORMAT);
        $this->endDateTime = $this->endDate->format(self::DATETIME_FORMAT);
    }

    public function getNextWeekInMonth()
    {
        $firstDayOfNextWeek = Carbon::createFromTimestamp($this->endTimestamp)->addDay();
        return new self($firstDayOfNextWeek);
    }

    public function calculate($method)
    {
        if (!isset(self::AVAILABLE_CALCULATE_METHODS[$method])) {
            throw new \DomainException('Нет недельного метода расчета "' . $method . '"');
        }

        $this->calculateMethod = $method;

        $this->average = $this->$method();
        try {
            $this->average = (float)number_format(floatval($this->average), 2, '.', '');
        } catch (\Exception $e) {
            $this->average = $this->$method();
        }
        $this->calculationMethod = self::AVAILABLE_CALCULATE_METHODS[$method];

        return $this->average;
    }

    private function weeklyCalculationMethod1()
    {
        $sum = 0;

        $values = Bitcoin::getPricesFromDB($this->startDate, $this->endDate);
        if (!$values) {
            return 0;
        }

        $count = 0;
        foreach ($values as $value) {
            if ($value->price > 0) {
                $sum += $value->price;
                $count ++;
            }
        }

        return floatval($sum / $count);
    }

    private function weeklyCalculationMethod2()
    {
        $values = Bitcoin::getPricesFromDB($this->startDate, $this->endDate);
        if (!$values) {
            return 0;
        }

        $min = 0;
        $max = 0;

        $timeStampOfMaximum = $values[0]->timestamp;
        $timeStampOfMinimum = $values[0]->timestamp;

        foreach ($values as $value) {
            // Установим значения, если их не было
            if ($min === 0) {
                $min = $value->price;
            }
            if ($max === 0) {
                $max = $value->price;
            }

            if ($min < $value->price) {
                $min = $value->price;
                $timeStampOfMinimum = $value->timestamp;
            }
            if ($max > $value->price) {
                $max = $value->price;
                $timeStampOfMaximum = $value->timestamp;
            }
        }

        $this->dateOfMaximum = Carbon::createFromFormat('Y-m-d H:i:s', $timeStampOfMaximum);
        $this->dateOfMinimum = Carbon::createFromFormat('Y-m-d H:i:s', $timeStampOfMinimum);

        $this->minValueOfPeriod = $min;
        $this->maxValueOfPeriod = $max;

        return ($max + $min) / 2;
    }
}
