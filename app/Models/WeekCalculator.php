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

    const DATETIME_FORMAT = 'd.m.Y H:i:s';

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
}
