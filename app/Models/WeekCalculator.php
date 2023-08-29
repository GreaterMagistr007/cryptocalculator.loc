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

    public string $calculationMethod;
    public float $average;

    const DATETIME_FORMAT = 'd.m.Y H:i:s';

    const AVAILABLE_CALCULATE_METHODS = [
        'weeklyCalculationMethod1' => '(сумма всех значений за период) / (количество значений)',
        'weeklyCalculationMethod2' => '(Минимальное + максимальное значение за период) / 2',
    ];

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

        foreach ($values as $value) {
            $sum += $value->price;
        }

        return floatval($sum / count($values));
    }

    private function weeklyCalculationMethod2()
    {
        $values = Bitcoin::getPricesFromDB($this->startDate, $this->endDate);
        if (!$values) {
            return 0;
        }

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

        return floatval(($min + $min) / 2);
    }
}
