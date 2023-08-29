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

    /**
     * @param $subMonths
     * @return array
     */
    public static function getDatesBySubmonth($subMonths = 2):array
    {
        $result = [
            'month' => []
        ];

        $month = new MonthCalculator(Carbon::now());

        for ($i = 0; $i < $subMonths; $i++) {
            $month = $month->getPreviousMonth();
            $result['month'][$month->getMonthTitle()] = $month;
        }

        return $result;
    }

    /**
     * @param Carbon $date
     * @return string
     */
    public static function getRussianMonth(Carbon $date):string
    {
        return isset(self::MONTH_TITLES_TRANSLATIONS[$date->format('F')]) ?
            self::MONTH_TITLES_TRANSLATIONS[$date->format('F')] :
            $date->format('F');
    }

    /**
     * @param Carbon $monthDate
     * @return array
     */
    public static function divideMonthByWeeks(Carbon $monthDate):array
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
