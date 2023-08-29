<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Date extends Model
{
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
    public static function getDatesBySubmonth($subMonths = 2)
    {
        $result = [];

        $dateEnd = Carbon::now()->endOfMonth()->subMonth();
        $dateStart = Carbon::now()->startOfMonth();

        $subMonths -= 1;
        while ($subMonths > 0) {
            $dateStart->subMonth();
            $subMonths -= 1;
        }

        $result = [
            'dateStart' => $dateStart->format('Y-m-d H:i:s'),
            'dateEnd' => $dateEnd->format('Y-m-d H:i:s'),
            'months' => self::dividePeriodByMonth($dateStart, $dateEnd)
        ];

//        foreach ($result['months']) {
//
//        }

        dd($result);

        return $result;
    }

    public static function dividePeriodByMonth(Carbon $dateStart, Carbon $dateEnd)
    {
        $result = [];

        $date = Carbon::createFromTimestamp($dateStart->getTimestamp());

        while ($date <= $dateEnd) {
            $date->startOfMonth();
            $result[$date->format('m')] = [
                'startTimestamp' => $date->getTimestamp(),
                'endTimestamp' => $date->endOfMonth()->getTimestamp(),
                'monthTitle' => self::getRussianMonth($date)
            ];
            $date->add(1, 'day');
            $date->startOfMonth();
        }

        $result[$date->format('m')] = [
            'startTimestamp' => $date->getTimestamp(),
            'endTimestamp' => $dateEnd->endOfMonth()->getTimestamp(),
            'monthTitle' => self::getRussianMonth($date)
        ];

        foreach ($result as $key => $month) {
            $result[$key]['weeks'] = self::divideMonthByWeeks(Carbon::createFromTimestamp($month['startTimestamp']));
        }

        return $result;
    }

    public static function getRussianMonth(Carbon $date)
    {
        return isset(self::MONTH_TITLES_TRANSLATIONS[$date->format('F')]) ?
            self::MONTH_TITLES_TRANSLATIONS[$date->format('F')] :
            $date->format('F');
    }

    public static function divideMonthByWeeks(Carbon $monthDate)
    {
        $firstMomentOfMonth = Carbon::createFromTimestamp($monthDate->startOfMonth()->getTimestamp());
        $lastMomentOfMonth = Carbon::createFromTimestamp($monthDate->endOfMonth()->getTimestamp());

        $result = [];

        $date =  Carbon::createFromTimestamp($firstMomentOfMonth->getTimestamp());

        $i = 1;
        while ($date <= $lastMomentOfMonth) {
            $nextDate = Carbon::createFromTimestamp($date->getTimestamp());
            $nextDate->addDays(6)->endOfDay();
            if ($nextDate->getTimestamp() >= $lastMomentOfMonth->getTimestamp()) {
                $nextDate = Carbon::createFromTimestamp($lastMomentOfMonth->getTimestamp());
            }
            $result[$i] = [
                'startTimestamp' => $date->getTimestamp(),
                'endTimestamp' => $nextDate->getTimestamp(),
                'startDateTime' => $date->format('d.m.Y H:i:s'),
                'endDateTime' => $nextDate->format('d.m.Y H:i:s'),
            ];

            $date = $nextDate->addDay()->startOfDay();
            $i++;
        }

        return $result;
    }
}
